<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Perintah CLI untuk migrasi dari clone WordPress. Hanya bisa dijalankan dari terminal.
 *
 *   php index.php tools convert <slug> [path/di/clone/index.html]
 *       Ubah halaman clone menjadi view CI + entri di config/pages.php.
 *       Default path: <slug>/index.html (slug "home" = index.html).
 *
 *   php index.php tools reconvert <slug> [path]
 *       Sama seperti convert, tetapi menimpa view halaman yang sudah ada.
 *
 *   php index.php tools check [folder]
 *       Uji coba tanpa menulis file: halaman clone mana yang cocok dengan layout bersama.
 *
 *   php index.php tools migrate
 *       Jalankan migrasi database (application/migrations/).
 *
 *   php index.php tools import_posts
 *       Impor post WordPress (clone + cache REST API) ke database. Aman diulang.
 *
 *   php index.php tools set_login <slug-author> <username> [admin|editor]
 *       Beri akses login panel admin ke author; password acak ditampilkan sekali.
 *
 *   php index.php tools verify [slug|all]
 *       Bandingkan hasil render CI (server harus jalan di base_url) dengan clone, byte per byte.
 */
class Tools extends CI_Controller {

	protected $guard = "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n";

	public function __construct()
	{
		parent::__construct();

		if ( ! is_cli())
		{
			show_404();
		}

		$this->load->library('wp_clone');
	}

	/**
	 * Jalankan migrasi database sampai versi di config/migration.php.
	 */
	public function migrate()
	{
		$this->load->library('migration');
		if ($this->migration->current() === FALSE)
		{
			$this->fail($this->migration->error_string());
		}
		echo "Migrasi selesai.\n";
	}

	/**
	 * Impor post, kategori, tag, author, dan media dari clone ke database.
	 */
	public function import_posts()
	{
		$this->load->library('wp_import');
		try
		{
			$pages = $this->wp_import->run();
		}
		catch (RuntimeException $e)
		{
			$this->fail($e->getMessage());
		}

		foreach ($this->wp_import->log() as $line)
		{
			echo '  '.$line."\n";
		}
		echo 'Impor selesai: '.count($pages)." post.\n";
	}

	/**
	 * Beri akses login admin ke author (password acak ditampilkan sekali).
	 *   php index.php tools set_login <slug-author> <username> [admin|editor]
	 */
	public function set_login($slug = NULL, $username = NULL, $role = 'admin')
	{
		$this->load->model('author_model');
		$author = $slug ? $this->db->where('slug', $slug)->get('authors')->row_array() : NULL;
		if ( ! $author OR ! $username OR ! preg_match('/^[a-zA-Z0-9_.-]{3,60}$/', $username))
		{
			$this->fail('Pakai: tools set_login <slug-author> <username> [admin|editor]');
		}
		if ($this->author_model->username_taken($username, $author['id']))
		{
			$this->fail('Username sudah dipakai.');
		}

		$password = rtrim(strtr(base64_encode(random_bytes(12)), '+/', '-_'), '=');
		$this->author_model->update($author['id'], array(
			'username'      => $username,
			'password_hash' => password_hash($password, PASSWORD_DEFAULT),
			'role'          => ($role === 'editor') ? 'editor' : 'admin',
			'is_active'     => 1,
		));

		echo "Login untuk {$author['display_name']}: username {$username}, password {$password}\n";
		echo "Segera ganti password lewat menu Profil saya.\n";
	}

	public function index()
	{
		echo "Perintah: convert <slug> [path] | reconvert <slug> [path] | check [folder] | verify [slug|all]\n";
	}

	public function convert($slug = NULL, ...$path)
	{
		$this->do_convert($slug, $path, FALSE);
	}

	public function reconvert($slug = NULL, ...$path)
	{
		$this->do_convert($slug, $path, TRUE);
	}

	protected function do_convert($slug, array $path, $overwrite)
	{
		if ($slug === NULL)
		{
			$this->fail('Slug wajib diisi.');
		}

		$rel = empty($path) ? ($slug === 'home' ? 'index.html' : $slug.'/index.html') : implode('/', $path);
		$view_file = APPPATH.'views/pages/'.$slug.'.php';

		if ( ! $overwrite && is_file($view_file))
		{
			$this->fail('View pages/'.$slug.'.php sudah ada. Pakai "reconvert" untuk menimpa.');
		}

		try
		{
			list($parts, $active, $hints, $params) = $this->prepare($rel);
		}
		catch (RuntimeException $e)
		{
			$this->fail($e->getMessage());
		}

		// Bagian yang sama di semua halaman: dibuat sekali, setelah itu harus identik.
		foreach (array('document_open', 'drawer', 'header', 'footer') as $name)
		{
			$this->shared_partial('layouts/partials/'.$name, $parts[$name]);
		}

		// Varian aset CSS/JS: dipakai ulang jika sudah ada yang identik.
		$head = preg_replace('/<link rel="canonical" href="[^"]*" \/>/', '<link rel="canonical" href="<?= $canonical ?>" />', $parts['head']);
		$foot = preg_replace('/data-gt-orig-url="[^"]*"/', 'data-gt-orig-url="<?= html_escape($gt_orig_url) ?>"', $parts['foot']);

		$page = array(
			'source'                 => $rel,
			'view'                   => 'pages/'.$slug,
			'title'                  => $parts['title'],
			'body_attrs'             => $parts['body_attrs'],
			'head'                   => $this->variant('head', $slug, $head),
			'foot'                   => $this->variant('foot', $slug, $foot),
			'menu_active'            => $active,
			'img_hints'              => $hints,
			'footer_logo_post_image' => $params['footer_logo_post_image'],
		);

		file_put_contents($view_file, $this->guard.$parts['content']);
		$this->save_page($slug, $page);

		echo "OK  {$rel} -> views/pages/{$slug}.php (head: {$page['head']}, foot: {$page['foot']})\n";
		echo "    Jalankan: php index.php tools verify {$slug}\n";
	}

	/**
	 * Simpan head & foot halaman clone sebagai varian bernama (untuk template post/arsip).
	 *   php index.php tools layout <nama> <path/di/clone/index.html>
	 */
	public function layout($name = NULL, ...$path)
	{
		if ($name === NULL OR empty($path))
		{
			$this->fail('Pakai: tools layout <nama> <path/di/clone/index.html>');
		}

		try
		{
			list($parts) = $this->prepare(implode('/', $path));
		}
		catch (RuntimeException $e)
		{
			$this->fail($e->getMessage());
		}

		foreach (array('document_open', 'drawer', 'header', 'footer') as $partial)
		{
			$this->shared_partial('layouts/partials/'.$partial, $parts[$partial]);
		}

		$variants = array(
			'head' => preg_replace('/<link rel="canonical" href="[^"]*" \/>/', '<link rel="canonical" href="<?= $canonical ?>" />', $parts['head']),
			'foot' => preg_replace('/data-gt-orig-url="[^"]*"/', 'data-gt-orig-url="<?= html_escape($gt_orig_url) ?>"', $parts['foot']),
		);
		foreach ($variants as $type => $content)
		{
			$file = APPPATH.'views/layouts/'.$type.'/'.$name.'.php';
			if (is_file($file) && file_get_contents($file) !== $this->guard.$content)
			{
				$this->fail("layouts/{$type}/{$name}.php sudah ada dengan isi berbeda.");
			}
			file_put_contents($file, $this->guard.$content);
			echo "OK  layouts/{$type}/{$name}.php\n";
		}
	}

	/**
	 * Uji coba tanpa menulis file: cek halaman clone mana saja yang cocok dengan layout bersama.
	 *   php index.php tools check [folder]
	 */
	public function check(...$dir)
	{
		$base = $this->wp_clone->root();
		$prefix = empty($dir) ? '' : implode('/', $dir).'/';
		$files = array();
		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base.$prefix, FilesystemIterator::SKIP_DOTS));
		foreach ($it as $file)
		{
			$rel = substr($file->getPathname(), strlen($base));
			if ($file->getFilename() === 'index.html' && ! preg_match('#^wp-|(^|/)feed/#', $rel))
			{
				$files[] = $rel;
			}
		}
		sort($files);

		$reasons = array();
		foreach ($files as $rel)
		{
			try
			{
				list($parts) = $this->prepare($rel);
				$reason = 'OK';
				foreach (array('document_open', 'drawer', 'header', 'footer') as $name)
				{
					$file = APPPATH.'views/layouts/partials/'.$name.'.php';
					if (is_file($file) && file_get_contents($file) !== $this->guard.$parts[$name])
					{
						$reason = 'beda di partial '.$name;
						break;
					}
				}
			}
			catch (RuntimeException $e)
			{
				$reason = $e->getMessage();
			}
			$reasons[$reason][] = $rel;
		}

		foreach ($reasons as $reason => $list)
		{
			echo str_pad(count($list), 5, ' ', STR_PAD_LEFT).'  '.$reason."\n";
			foreach (array_slice($list, 0, 5) as $rel)
			{
				echo '       '.$rel."\n";
			}
		}
	}

	public function verify($slug = 'all')
	{
		$this->config->load('pages');
		$pages = $this->config->item('pages');
		$slugs = ($slug === 'all') ? array_keys($pages) : array($slug);
		$failed = 0;

		foreach ($slugs as $s)
		{
			if ( ! isset($pages[$s]))
			{
				$this->fail('Halaman "'.$s.'" belum ada di config/pages.php.');
			}

			$url = site_url($s === 'home' ? '' : $s);
			$actual = @file_get_contents($url, FALSE, stream_context_create(array('http' => array('ignore_errors' => TRUE))));
			if ($actual === FALSE)
			{
				$this->fail('Tidak bisa membuka '.$url.'. Jalankan dulu: php -S localhost:8000 server.php');
			}

			$expected = $this->wp_clone->expected($pages[$s]['source']);
			$missing = $this->missing_assets($expected);

			if ($actual === $expected)
			{
				echo "OK    {$s}\n";
			}
			else
			{
				$failed++;
				echo "BEDA  {$s}\n".$this->describe_diff($expected, $actual);
			}

			foreach ($missing as $file)
			{
				echo "      aset tidak ada: {$file}\n";
			}
		}

		exit($failed > 0 ? 1 : 0);
	}

	/**
	 * Clean + rewrite URL + pecah halaman + netralkan menu aktif & atribut gambar.
	 */
	protected function prepare($rel)
	{
		$clone = $this->wp_clone;
		$html = $clone->clean($clone->read($rel));
		$html = $clone->rewrite_urls($clone->escape_php($html), $rel, 'php');
		$parts = $clone->split($html);

		$active = array();
		$hints = array();
		$params = array();
		foreach (array('drawer', 'header', 'footer', 'foot') as $name)
		{
			if (isset($parts[$name])) {
				$parts[$name] = $clone->neutralize_contacts($parts[$name]);
			}
		}

		foreach (array('drawer', 'header', 'footer') as $name)
		{
			$parts[$name] = $clone->neutralize_menu($parts[$name], $active);
			$parts[$name] = $clone->neutralize_img_hints($parts[$name], $name, $hints);
		}
		$parts['footer'] = $clone->neutralize_footer($parts['footer'], $params);

		return array($parts, $active, $hints, $params);
	}

	/**
	 * Bandingkan semua halaman post & arsip (dari database) dengan clone.
	 *   php index.php tools verify_db [single-post|archive] [detail]
	 */
	public function verify_db($type = NULL, $detail = NULL)
	{
		$base = $this->wp_clone->root();
		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
		$ok = 0;
		$failed = array();

		foreach ($it as $file)
		{
			$rel = substr($file->getPathname(), strlen($base));
			if ($file->getFilename() !== 'index.html' OR preg_match('#^wp-|(^|/)feed/#', $rel))
			{
				continue;
			}
			$head = file_get_contents($file->getPathname(), FALSE, NULL, 0, 200000);
			if ( ! preg_match('/<body class="([^"]*)"/', $head, $m))
			{
				continue;
			}
			$kind = (strpos($m[1], 'single-post') !== FALSE) ? 'single-post' : ((strpos($m[1], 'archive') !== FALSE) ? 'archive' : NULL);
			if ($kind === NULL OR ($type !== NULL && $type !== $kind))
			{
				continue;
			}

			$url = site_url($this->wp_clone->real_path(dirname($rel)));
			$actual = @file_get_contents($url, FALSE, stream_context_create(array('http' => array('ignore_errors' => TRUE))));
			if ($actual === FALSE)
			{
				$this->fail('Tidak bisa membuka '.$url.'. Jalankan dulu: php -S localhost:8000 server.php');
			}

			$expected = $this->wp_clone->expected($rel);
			if ($actual === $expected)
			{
				$ok++;
			}
			else
			{
				$failed[] = array($rel, $this->describe_diff($expected, $actual));
			}
		}

		echo "OK {$ok}, BEDA ".count($failed)."\n";
		foreach (array_slice($failed, 0, $detail === NULL ? 5 : 1000) as $f)
		{
			echo "BEDA  {$f[0]}\n{$f[1]}";
		}

		exit(empty($failed) ? 0 : 1);
	}

	protected function shared_partial($view, $content)
	{
		$file = APPPATH.'views/'.$view.'.php';
		$content = $this->guard.$content;

		if ( ! is_file($file))
		{
			file_put_contents($file, $content);
			echo "Buat {$view}.php\n";
			return;
		}

		$existing = file_get_contents($file);
		if ($existing !== $content)
		{
			$this->fail("Bagian {$view} di halaman ini berbeda dari layout bersama:\n".$this->describe_diff($existing, $content));
		}
	}

	protected function variant($type, $slug, $content)
	{
		$content = $this->guard.$content;

		foreach (glob(APPPATH.'views/layouts/'.$type.'/*.php') as $file)
		{
			if (file_get_contents($file) === $content)
			{
				return basename($file, '.php');
			}
		}

		$name = $slug;
		for ($i = 2; is_file(APPPATH.'views/layouts/'.$type.'/'.$name.'.php'); $i++)
		{
			$name = $slug.'-'.$i;
		}

		file_put_contents(APPPATH.'views/layouts/'.$type.'/'.$name.'.php', $content);
		echo "Buat layouts/{$type}/{$name}.php\n";

		return $name;
	}

	protected function save_page($slug, array $page)
	{
		$this->config->load('pages');
		$pages = $this->config->item('pages');
		$pages[$slug] = $page;
		ksort($pages);

		$code = "<?php\ndefined('BASEPATH') OR exit('No direct script access allowed');\n\n"
			."/*\n| Daftar halaman statis. File ini DIHASILKAN oleh: php index.php tools convert <slug>\n"
			."| Kunci = slug URL ('home' untuk halaman depan).\n*/\n"
			.'$config[\'pages\'] = '.var_export($pages, TRUE).";\n";

		file_put_contents(APPPATH.'config/pages.php', $code);
	}

	protected function missing_assets($html)
	{
		$base = preg_quote(base_url(), '~');
		preg_match_all('~'.$base.'(wp-(?:content|includes)/[^"\'\s?#)]+)~', $html, $m);

		$missing = array();
		foreach (array_unique($m[1]) as $path)
		{
			if ( ! is_file(FCPATH.rawurldecode($path)))
			{
				$missing[] = $path;
			}
		}

		return $missing;
	}

	protected function describe_diff($expected, $actual)
	{
		$len = min(strlen($expected), strlen($actual));
		for ($i = 0; $i < $len && $expected[$i] === $actual[$i]; $i++);

		$line = substr_count($expected, "\n", 0, $i) + 1;
		$start = max(0, $i - 120);

		return "      panjang: seharusnya ".strlen($expected).", didapat ".strlen($actual)."; beda pertama di baris {$line}\n"
			."      seharusnya: ".json_encode(substr($expected, $start, 240), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n"
			."      didapat   : ".json_encode(substr($actual, $start, 240), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";
	}

	protected function fail($message)
	{
		fwrite(STDERR, "GAGAL: {$message}\n");
		exit(1);
	}
}
