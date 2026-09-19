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
 *   php index.php tools import_downloads [ulang]
 *       Impor paket Download Manager dari clone ke tabel downloads.
 *
 *   php index.php tools import_pages
 *       Pindahkan halaman statis dari config/pages.php ke tabel pages (aman diulang).
 *
 *   php index.php tools import_media
 *       Daftarkan file di wp-content/uploads ke pustaka media (aman diulang).
 *
 *   php index.php tools download_shortcodes
 *       Ganti salinan kartu Download Manager di halaman & post dengan [wpdm_package id='N'].
 *
 *   php index.php tools set_login <slug-author> <username> [admin|editor]
 *       Beri akses login panel admin ke author; password acak ditampilkan sekali.
 *
 *   php index.php tools verify [slug|all] [dump]
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
	 * Impor paket Download Manager dari clone. Menolak jika tabel downloads sudah berisi,
	 * kecuali dengan argumen "ulang" (data yang diubah lewat admin akan hilang).
	 *   php index.php tools import_downloads [ulang]
	 */
	public function import_downloads($force = NULL)
	{
		if ($force !== 'ulang' && $this->db->count_all('downloads') > 0)
		{
			$this->fail('Tabel downloads sudah berisi. Pakai "import_downloads ulang" untuk menimpa (perubahan dari admin hilang).');
		}

		$this->load->library('wpdm_import');
		try
		{
			$rows = $this->wpdm_import->run();
		}
		catch (RuntimeException $e)
		{
			$this->fail($e->getMessage());
		}

		$assumed = 0;
		foreach ($this->wpdm_import->log() as $line)
		{
			if (strpos($line, 'diasumsikan') !== FALSE)
			{
				$assumed++;
				continue;
			}
			echo '  '.$line."\n";
		}
		$external = count(array_filter($rows, function ($r) { return preg_match('#^https?://#', $r['file']); }));
		echo 'Impor selesai: '.count($rows).' paket ('.$external.' file di situs luar, '.$assumed." diasumsikan = PDF deskripsi).\n";
	}

	/**
	 * Masukkan halaman statis dari config/pages.php ke tabel pages. Aman diulang: hanya halaman yang masih
	 * berupa file view (ada di config/pages.php) yang diproses; halaman yang sudah dikelola admin tidak disentuh.
	 *
	 * - Halaman berkerangka standar (hero + entry-content + share + sidebar): isi entry-content diambil dari
	 *   view-nya, disimpan dengan view NULL (dikelola dari /admin/pages), lalu dihapus dari config/pages.php.
	 *   File view lamanya tidak dipakai lagi.
	 * - Template "Elementor Full Width" (<main> + konten Elementor saja, mis. statistik): juga ke database, template full-width.
	 * - Beranda: tetap berupa view; baris pages hanya
	 *   untuk pencarian & pilihan menu (content dari wp-json/wp/v2/pages, clone atau cache REST API live).
	 *
	 *   php index.php tools import_pages
	 */
	public function import_pages()
	{
		$this->load->helper('admin');
		$this->config->load('pages');
		$pages = $this->config->item('pages');
		$open = '<div class="entry-content is-layout-flow">';
		$tail = NULL;
		$managed = array();

		foreach ($pages as $key => $page)
		{
			if ($key === 'error-404' OR ! preg_match('/\bpage-id-(\d+)\b/', $page['body_attrs'], $m))
			{
				continue;
			}
			$id = (int) $m[1];
			$json = $this->page_json($id, $key);

			// Judul mentah: dibalik dari hasil wptexturize dan dipastikan sama persis setelah wp_texturize().
			$rendered = $json['title']['rendered'];
			$raw = html_entity_decode(strtr(preg_replace('/(?<=^| )&#8211;(?=$| )/', '-', $rendered), array(
				'&#8211;' => '--', '&#8212;' => '---', '&#8220;' => '"', '&#8221;' => '"', '&#8216;' => "'",
				'&#8217;' => "'", '&#8230;' => '...', '&#038;' => '&', '&#215;' => 'x',
			)), ENT_QUOTES, 'UTF-8');
			if (wp_texturize($raw) !== $rendered)
			{
				$this->fail('Judul mentah tidak cocok untuk page '.$id.': '.$rendered);
			}

			$featured = (int) $json['featured_media'];
			if ($featured && ! $this->db->where('id', $featured)->count_all_results('media'))
			{
				$this->fail('Gambar unggulan page '.$id.' (media '.$featured.') belum ada di tabel media.');
			}

			$row = array(
				'id'                => $id,
				'slug'              => $key,
				'title'             => $raw,
				'content'           => $this->wp_clone->rewrite_urls($json['content']['rendered'], 'page/index.html', 'token'),
				'view'              => $page['view'],
				'template'          => 'default',
				'author_id'         => (int) $json['author'],
				'featured_media_id' => $featured ? $featured : NULL,
				'status'            => 'publish',
				'layout_head'       => NULL,
				'layout_foot'       => NULL,
				'published_at'      => str_replace('T', ' ', $json['date']),
				'modified_at'       => str_replace('T', ' ', $json['modified']),
			);

			// Kerangka standar: isi entry-content dari view (sudah terverifikasi identik dengan clone).
			$html = $this->load->view($page['view'], array(), TRUE);
			$full_open = "\n\t<main id=\"main\" class=\"site-main hfeed\">\n\n\t\t\t\t";
			$full_close = "\n\t\t\t</main>\n\n\t";
			$a = strpos($html, $open);
			$e = strpos($html, '<div class="ct-share-box');
			if ($a !== FALSE && $e !== FALSE && strpos($html, '<div class="hero-section" data-type="type-2">') !== FALSE)
			{
				$region = substr($html, $a + strlen($open), $e - $a - strlen($open));
				$cut = strrpos($region, '</div>');
				if ($tail === NULL)
				{
					$tail = substr($region, $cut);
				}
				elseif (substr($region, $cut) !== $tail)
				{
					$this->fail('Penutup entry-content halaman '.$key.' berbeda dari halaman lain.');
				}
				$row['content'] = content_to_tokens(substr($region, 0, $cut));
				$row['view'] = NULL;
				$row['layout_head'] = $page['head'];
				$row['layout_foot'] = $page['foot'];
				$managed[] = $key;
			}
			// Template "Elementor Full Width" (elementor_header_footer): <main> berisi konten Elementor saja.
			elseif ($key !== 'home' && strpos($page['body_attrs'], 'page-template-elementor_header_footer') !== FALSE
				&& strpos($html, $full_open) === 0 && substr($html, -strlen($full_close)) === $full_close)
			{
				$row['content'] = content_to_tokens(substr($html, strlen($full_open), -strlen($full_close)));
				$row['view'] = NULL;
				$row['template'] = 'full-width';
				$row['layout_head'] = $page['head'];
				$row['layout_foot'] = $page['foot'];
				$managed[] = $key;
			}

			$this->db->replace('pages', $row);
			echo ($row['view'] === NULL ? 'DB    ' : 'VIEW  ').$key."\n";
		}

		// Halaman yang sudah pindah ke database tidak lagi dirender dari config/pages.php.
		if ($managed)
		{
			$this->write_pages(array_diff_key($pages, array_flip($managed)));
		}
		echo 'pages: '.count($managed).' halaman dipindah ke database; view lamanya (views/pages/<slug>.php) tidak dipakai lagi.'."\n";
	}

	/**
	 * Ganti salinan statis kartu Download Manager ("WPDM Link Template") di konten halaman & post dengan
	 * [wpdm_package id='N'], hanya jika kartu hasil render dari tabel downloads sama persis dengan salinannya
	 * (parameter refresh diabaikan). Ikon yang berbeda dari aturan otomatis disimpan ke downloads.icon.
	 * Kartu yang tidak bisa dibuat ulang (mis. ikon dari situs lain) dibiarkan. Aman diulang.
	 *
	 *   php index.php tools download_shortcodes
	 */
	public function download_shortcodes()
	{
		$this->load->model('download_model');
		$this->load->helper('admin');
		$downloads = array();
		foreach ($this->db->get('downloads')->result_array() as $d)
		{
			$downloads[$d['id']] = $d;
		}

		$converted = 0;
		$kept = array();
		foreach (array('pages' => 'view IS NULL', 'posts' => '1 = 1') as $table => $where)
		{
			foreach ($this->db->select('id, content')->where($where, NULL, FALSE)->get($table)->result_array() as $row)
			{
				$texturize = (strpos($row['content'], 'data-elementor-type=') !== FALSE);
				$content = preg_replace_callback("#<div class='w3eden'><!-- WPDM Link Template: Default Template -->.*?\n</div>\n\n</div>#s",
					function ($m) use (&$downloads, &$converted, &$kept, $table, $row, $texturize) {
						$card = wp_content($m[0]);
						if ( ! preg_match('/wpdmdl=(\d+)&amp;refresh=([0-9a-f]+)"/', $card, $x) OR ! isset($downloads[$x[1]])
							OR $downloads[$x[1]]['status'] !== 'publish')
						{
							$kept[] = $table.' '.$row['id'].': paket tidak ditemukan / draft';
							return $m[0];
						}
						$d = $downloads[$x[1]];
						// Ikon WordPress berbeda dari aturan otomatis (mis. Google Drive berisi PDF): simpan ke downloads.icon.
						if (preg_match('#file-type-icons/([a-z0-9]+)\.svg#', $card, $icon) && $icon[1] !== Download_model::icon($d)
							&& $this->download_model->card(array_merge($d, array('icon' => $icon[1])), $x[2], $texturize) === $card)
						{
							$this->db->update('downloads', array('icon' => $icon[1]), array('id' => $d['id']));
							$downloads[$d['id']]['icon'] = $d['icon'] = $icon[1];
						}
						if ($this->download_model->card($d, $x[2], $texturize) !== $card)
						{
							$kept[] = $table.' '.$row['id'].': paket '.$d['id'].' (kartu berbeda dari render database)';
							return $m[0];
						}
						$converted++;
						return "[wpdm_package id='".$d['id']."']";
					}, $row['content']);

				if ($content !== $row['content'])
				{
					$this->db->update($table, array('content' => $content), array('id' => $row['id']));
				}
			}
		}

		echo 'Kartu diganti kode pendek: '.$converted."\n";
		foreach ($kept as $k)
		{
			echo '  dibiarkan: '.$k."\n";
		}
	}

	/**
	 * Daftarkan file di wp-content/uploads/YYYY/MM/ yang belum ada di pustaka media (tabel media), supaya bisa dipilih
	 * ulang dari admin. Ukuran turunan (-WxH, -scaled) dikelompokkan ke file aslinya dan diberi nama ukuran WordPress;
	 * ID & alt diambil dari <img class="wp-image-N"> di konten/view bila ada. File yang sudah terdaftar tidak disentuh,
	 * jadi aman diulang. File 404 hasil HTTrack (.html) dan folder plugin (elementor, blocksy, download-manager-files) dilewati.
	 *
	 *   php index.php tools import_media
	 */
	public function import_media()
	{
		$this->load->model('media_model');
		$base = FCPATH.'wp-content/uploads/';
		$mimes = array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp',
			'pdf' => 'application/pdf', 'doc' => 'application/msword', 'xls' => 'application/vnd.ms-excel', 'ppt' => 'application/vnd.ms-powerpoint',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'zip' => 'application/zip', 'mp4' => 'video/mp4');

		// File yang sudah terdaftar (asli + semua ukuran).
		$known = array();
		$used_ids = array();
		foreach ($this->db->get('media')->result_array() as $m)
		{
			$used_ids[(int) $m['id']] = TRUE;
			foreach ($this->media_model->files($m) as $f)
			{
				$known[substr($f, strlen($base))] = TRUE;
			}
		}

		// ID attachment WordPress & alt dari <img class="wp-image-N"> di konten dan view.
		$html = '';
		foreach (array('posts' => 'content', 'pages' => 'content', 'downloads' => 'description') as $table => $col)
		{
			foreach ($this->db->select($col)->get($table)->result_array() as $r)
			{
				$html .= $r[$col];
			}
		}
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(APPPATH.'views', FilesystemIterator::SKIP_DOTS)) as $f)
		{
			$html .= file_get_contents($f->getPathname());
		}
		$refs = array();
		preg_match_all('/<img\b[^>]*>/i', $html, $imgs);
		foreach ($imgs[0] as $img)
		{
			if ( ! preg_match('#wp-content/uploads/(\d{4}/\d{2}/[^"\'\s?]+)#', $img, $src))
			{
				continue;
			}
			$orig = preg_replace('/-(?:\d+x\d+|scaled)(?=\.[a-z0-9]+$)/i', '', $src[1]);
			$id = preg_match('/\bwp-image-(\d+)\b/', $img, $x) ? (int) $x[1] : NULL;
			$alt = preg_match('/\salt="([^"]*)"/', $img, $a) ? html_entity_decode($a[1], ENT_QUOTES, 'UTF-8') : '';
			if ( ! isset($refs[$orig]))
			{
				$refs[$orig] = array('id' => NULL, 'alt' => '');
			}
			if ($refs[$orig]['id'] === NULL && $id)
			{
				$refs[$orig]['id'] = $id;
			}
			if ($refs[$orig]['alt'] === '' && $alt !== '')
			{
				$refs[$orig]['alt'] = $alt;
			}
		}

		// Kelompokkan file per folder: asli + ukuran turunan.
		$files = array();
		foreach (glob($base.'[0-9][0-9][0-9][0-9]/[0-9][0-9]/*') as $path)
		{
			$rel = substr($path, strlen($base));
			$ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
			if (is_file($path) && isset($mimes[$ext]))
			{
				$files[$rel] = TRUE;
			}
		}
		$groups = array();
		foreach (array_keys($files) as $rel)
		{
			if (preg_match('/^(.*)-(\d+)x(\d+)(\.[a-z0-9]+)$/i', $rel, $m) && (isset($files[$m[1].$m[4]]) OR isset($files[$m[1].'-scaled'.$m[4]])))
			{
				continue;
			}
			if (preg_match('/^(.*)-scaled(\.[a-z0-9]+)$/i', $rel) === 0 && preg_match('/^(.*)(\.[a-z0-9]+)$/i', $rel, $m) && isset($files[$m[1].'-scaled'.$m[2]]))
			{
				continue; // file asli sebelum di-scale ikut kelompok "-scaled"
			}
			$stem = preg_replace('/(-scaled)?(\.[a-z0-9]+)$/i', '', $rel);
			$ext = pathinfo($rel, PATHINFO_EXTENSION);
			$variants = array();
			foreach (array_keys($files) as $other)
			{
				if (preg_match('/^'.preg_quote($stem, '/').'-(\d+)x(\d+)\.'.preg_quote($ext, '/').'$/i', $other, $v))
				{
					$variants[$other] = array((int) $v[1], (int) $v[2]);
				}
			}
			$groups[$rel] = $variants;
		}

		$added = 0;
		$skipped = 0;
		$rows = array();
		foreach ($groups as $rel => $variants)
		{
			$all = array_merge(array($rel), array_keys($variants));
			$registered = FALSE;
			foreach ($all as $f)
			{
				$registered = $registered || isset($known[$f]);
			}
			if ($registered)
			{
				$skipped++;
				continue;
			}

			$ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
			$mime = $mimes[$ext];
			$width = $height = 0;
			$sizes = array();
			if (strpos($mime, 'image/') === 0 && ($info = @getimagesize($base.$rel)))
			{
				list($width, $height) = $info;
				$sizes = $this->wp_size_names($width, $height, $variants);
			}
			$orig = preg_replace('/-scaled(?=\.[a-z0-9]+$)/i', '', $rel);
			$ref = isset($refs[$orig]) ? $refs[$orig] : array('id' => NULL, 'alt' => '');
			$row = array(
				'file'       => $rel,
				'width'      => $width,
				'height'     => $height,
				'alt'        => mb_substr($ref['alt'], 0, 255),
				'mime_type'  => $mime,
				'sizes'      => json_encode($sizes, JSON_UNESCAPED_SLASHES),
				'created_at' => substr($rel, 0, 4).'-'.substr($rel, 5, 2).'-01 00:00:00',
			);
			if ($ref['id'] && ! isset($used_ids[$ref['id']]))
			{
				$row['id'] = $ref['id'];
				$used_ids[$ref['id']] = TRUE;
			}
			$rows[] = $row;
		}

		// ID WordPress lebih dulu, lalu sisanya mendapat ID baru setelah ID terbesar.
		usort($rows, function ($a, $b) { return isset($b['id']) - isset($a['id']); });
		$this->db->trans_start();
		foreach ($rows as $row)
		{
			$this->db->insert('media', $row);
			$added++;
		}
		$this->db->trans_complete();

		$with_id = count(array_filter($rows, function ($r) { return isset($r['id']); }));
		echo "Media ditambahkan: {$added} ({$with_id} dengan ID WordPress dari konten). Sudah terdaftar: {$skipped}.\n";
	}

	/**
	 * Nama ukuran WordPress untuk file turunan: thumbnail 150x150 (crop), medium 300, large 1024, medium_large 768 (lebar),
	 * 1536x1536, 2048x2048 (proporsional, toleransi 1px). Lainnya memakai nama "WxH". Urutan seperti metadata WordPress.
	 */
	protected function wp_size_names($w, $h, array $variants)
	{
		$boxes = array('medium' => array(300, 300), 'large' => array(1024, 1024), 'thumbnail' => NULL, 'medium_large' => array(768, 0),
			'1536x1536' => array(1536, 1536), '2048x2048' => array(2048, 2048));
		$sizes = array();
		$named = array();
		foreach ($boxes as $name => $box)
		{
			foreach ($variants as $file => $dim)
			{
				if ($box === NULL)
				{
					$match = ($dim[0] === 150 && $dim[1] === 150);
				}
				else
				{
					$ratio = min($box[0] / $w, $box[1] ? $box[1] / $h : INF);
					$match = ($ratio < 1 && abs(round($w * $ratio) - $dim[0]) <= 1 && abs(round($h * $ratio) - $dim[1]) <= 1);
				}
				if ($match)
				{
					$sizes[$name] = array('file' => basename($file), 'width' => $dim[0], 'height' => $dim[1]);
					$named[$file] = TRUE;
					break;
				}
			}
		}
		foreach ($variants as $file => $dim)
		{
			if ( ! isset($named[$file]))
			{
				$sizes[$dim[0].'x'.$dim[1]] = array('file' => basename($file), 'width' => $dim[0], 'height' => $dim[1]);
			}
		}

		return $sizes;
	}

	/**
	 * JSON page WordPress dari clone (wp-json/wp/v2/pages/<ID>.json) atau REST API live (di-cache).
	 */
	protected function page_json($id, $key)
	{
		$file = $this->wp_clone->root().'wp-json/wp/v2/pages/'.$id.'.json';
		$cache = APPPATH.'cache/wp-api/pages/'.$id.'.json';
		if ( ! is_file($file))
		{
			if ( ! is_file($cache))
			{
				$body = @file_get_contents('https://stmi.ac.id/wp-json/wp/v2/pages/'.$id);
				if ($body === FALSE OR json_decode($body) === NULL)
				{
					$this->fail('Gagal mengambil data page '.$id.' ('.$key.') dari API live.');
				}
				if ( ! is_dir(dirname($cache)))
				{
					mkdir(dirname($cache), 0775, TRUE);
				}
				file_put_contents($cache, $body);
				echo "  ambil dari live: pages/{$id}\n";
			}
			$file = $cache;
		}

		return json_decode(file_get_contents($file), TRUE);
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
			'img_hints'              => $hints,
			'footer_logo_post_image' => $params['footer_logo_post_image'],
		);

		// URL asli halaman di clone (mis. halaman 404 yang tersimpan dari /js15_as.js), untuk tools verify.
		if (preg_match('/data-gt-orig-url="([^"<]*)"/', $parts['foot'], $gt) && $gt[1] !== ($slug === 'home' ? '/' : '/'.$slug.'/'))
		{
			$page['verify_url'] = ltrim($gt[1], '/');
		}

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

	public function verify($slug = 'all', $dump = NULL)
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

			$url = site_url(isset($pages[$s]['verify_url']) ? $pages[$s]['verify_url'] : ($s === 'home' ? '' : $s));
			$actual = @file_get_contents($url, FALSE, stream_context_create(array('http' => array('ignore_errors' => TRUE))));
			if ($actual === FALSE)
			{
				$this->fail('Tidak bisa membuka '.$url.'. Jalankan dulu: php -S localhost:8000 server.php');
			}

			$expected = $this->wp_clone->expected($pages[$s]['source']);
			$missing = $this->missing_assets($expected);
			if ($dump === 'dump')
			{
				// Simpan keduanya untuk dibandingkan (diff/difflib): application/cache/verify/<slug>.{expected,actual}.html
				@mkdir(APPPATH.'cache/verify', 0775, TRUE);
				file_put_contents(APPPATH.'cache/verify/'.$s.'.expected.html', $expected);
				file_put_contents(APPPATH.'cache/verify/'.$s.'.actual.html', $actual);
			}

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
			$parts[$name] = $clone->neutralize_main_menu($clone->neutralize_menu($parts[$name], $active));
			$parts[$name] = $clone->neutralize_img_hints($parts[$name], $name, $hints);
		}
		$parts['footer'] = $clone->neutralize_footer($clone->neutralize_footer_links($parts['footer']), $params);

		return array($parts, $active, $hints, $params);
	}

	/**
	 * Bandingkan semua halaman post & arsip (dari database) dengan clone.
	 *   php index.php tools verify_db [single-post|single-wpdmpro|archive|page] [detail]
	 */
	public function verify_db($type = NULL, $detail = NULL)
	{
		$base = $this->wp_clone->root();
		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
		$ok = 0;
		$failed = array();
		$db_pages = array();
		foreach ($this->db->select('id')->where('view', NULL)->get('pages')->result_array() as $r)
		{
			$db_pages[(int) $r['id']] = FALSE;
		}

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
			$kind = (strpos($m[1], 'single-post') !== FALSE) ? 'single-post'
				: ((strpos($m[1], 'single-wpdmpro') !== FALSE) ? 'single-wpdmpro' : ((strpos($m[1], 'archive') !== FALSE) ? 'archive' : NULL));
			// Halaman statis yang dikelola dari database (tabel pages, view NULL).
			if ($kind === NULL && preg_match('/\bpage-template(?:-default| page-template-elementor_header_footer) page page-id-(\d+)\b/', $m[1], $pm) && isset($db_pages[(int) $pm[1]]))
			{
				$kind = 'page';
				$db_pages[(int) $pm[1]] = TRUE;
			}
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
			if ($kind === 'single-wpdmpro' OR $kind === 'page' OR $kind === 'single-post')
			{
				// Parameter refresh tombol Download dibuat acak tiap halaman dimuat (uniqid + time), juga di WordPress.
				$expected = preg_replace('/(data-downloadurl="[^"]*refresh=)[0-9a-f]{13}\d{10}/', '$1R', $expected);
				$actual = preg_replace('/(data-downloadurl="[^"]*refresh=)[0-9a-f]{13}\d{10}/', '$1R', $actual);
			}
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
		if ($type === NULL OR $type === 'page')
		{
			// Halaman baru dari admin tidak punya pembanding di clone.
			$unchecked = array_keys(array_filter($db_pages, function ($seen) { return ! $seen; }));
			if ($unchecked)
			{
				echo 'Halaman database tanpa pembanding di clone (ID): '.implode(', ', $unchecked)."\n";
			}
		}
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
		$this->write_pages($pages);
	}

	protected function write_pages(array $pages)
	{
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
