<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Alat bantu migrasi dari hasil clone HTTrack (stmi.ac.id-clone/) ke view CodeIgniter.
 *
 * Dipakai oleh controller CLI Tools untuk:
 *  - membersihkan jejak HTTrack,
 *  - mengubah URL relatif menjadi base_url()/site_url(),
 *  - memecah halaman menjadi bagian layout (head, drawer, header, konten, footer, foot).
 *
 * Mode 'php'     : URL ditulis sebagai kode PHP (<?= base_url('...') ?>) untuk disimpan ke view.
 * Mode 'literal' : URL ditulis sebagai URL jadi, untuk membandingkan dengan hasil render CI.
 */
class Wp_clone {

	/** ID widget GTranslate dibuat tetap (di clone ID-nya acak per halaman). */
	protected $gt_ids = array('73208722', '35407523');

	/** Domain asli WordPress (http/https, dengan/tanpa www). Subdomain lain tidak termasuk. */
	protected $origin = '(?:https?:)?//(?:www\.)?stmi\.ac\.id(?![\w.-])';

	/** Path di domain asli yang tidak pernah diubah: endpoint WordPress yang tidak ada di CI. */
	protected $wp_only = '#^(wp-admin|wp-json|wp-login\.php|xmlrpc\.php|feed)(/|$)#';

	protected $root;

	public function __construct()
	{
		$this->root = FCPATH.'stmi.ac.id-clone/';
	}

	public function root()
	{
		return $this->root;
	}

	public function read($rel)
	{
		$file = $this->root.$rel;
		if ( ! is_file($file))
		{
			throw new RuntimeException('File clone tidak ditemukan: '.$rel);
		}

		return file_get_contents($file);
	}

	/**
	 * Hasil akhir halaman clone seperti yang seharusnya dirender CI (mode literal).
	 */
	public function expected($rel)
	{
		$html = $this->clean($this->read($rel));

		return $this->rewrite_urls($html, $rel, 'literal');
	}

	/**
	 * Hapus komentar/meta HTTrack, link API WordPress, dan samakan ID GTranslate.
	 */
	public function clean($html)
	{
		$html = preg_replace('/<!-- Mirrored from [^>]*-->\r?\n?/', '', $html);
		$html = preg_replace('/<!-- Added by HTTrack -->.*?<!-- \/Added by HTTrack -->\r?\n?/s', '', $html);

		// Link internal WordPress (REST API, EditURI, shortlink, oEmbed): tidak tampil dan endpoint-nya tidak ada di CI.
		$html = preg_replace('/<link rel="https:\/\/api\.w\.org\/"[^>]*\/>(?:<link rel="alternate" title="JSON"[^>]*\/>)?(?:<link rel="EditURI"[^>]*\/>)?\r?\n?/', '', $html);
		$html = preg_replace('/<link rel=\'shortlink\'[^>]*\/>\r?\n?/', '', $html);
		$html = preg_replace('/<link rel="alternate" title="oEmbed \((?:JSON|XML)\)"[^>]*\/>\r?\n?/', '', $html);

		// URL eksternal yang dirusak HTTrack menjadi path relatif: kembalikan ke bentuk aslinya.
		$html = preg_replace('#(?:\.\./)+((?:s10|sstatic1)\.histats\.com)/#', '//$1/', $html);
		$html = preg_replace('#(?:\.\./)+(public\.tableau\.com)/#', 'https://$1/', $html);
		$html = str_replace('//sstatic1.histats.com/0dd88.gif?', '//sstatic1.histats.com/0.gif?', $html);

		preg_match_all('/gt-wrapper-(\d+)/', $html, $m);
		$map = array();
		foreach (array_values(array_unique($m[1])) as $i => $id)
		{
			$map[$id] = isset($this->gt_ids[$i]) ? $this->gt_ids[$i] : $id;
		}

		return strtr($html, $map);
	}

	/**
	 * Amankan tag pembuka PHP yang ada di HTML asli (mis. "<?xml") sebelum disimpan sebagai view.
	 */
	public function escape_php($html)
	{
		return str_replace('<?', "<?= '<?' ?>", $html);
	}

	/**
	 * Ubah semua URL lokal menjadi base_url()/site_url().
	 */
	public function rewrite_urls($html, $page_rel, $mode)
	{
		$dir = dirname($page_rel);
		$dir = ($dir === '.') ? '' : $dir.'/';
		$self = $this;

		// Atribut berisi URL
		$html = preg_replace_callback(
			'/(\s(?:href|src|data-src|data-downloadurl|poster|action)=|\scontent=(?=["\'](?:\.\.\/)*wp-(?:content|includes)\/))(["\'])([^"\']*)\2/',
			function ($m) use ($self, $dir, $mode) {
				return $m[1].$m[2].$self->map_url($m[3], $dir, $mode).$m[2];
			},
			$html
		);

		// srcset: "url 300w, url 600w"
		$html = preg_replace_callback(
			'/(\s(?:srcset|data-srcset)=)(["\'])([^"\']*)\2/',
			function ($m) use ($self, $dir, $mode) {
				$parts = explode(',', $m[3]);
				foreach ($parts as $i => $part)
				{
					if (preg_match('/^(\s*)(\S+)(.*)$/s', $part, $p))
					{
						$parts[$i] = $p[1].$self->map_url($p[2], $dir, $mode).$p[3];
					}
				}

				return $m[1].$m[2].implode(',', $parts).$m[2];
			},
			$html
		);

		// CSS url(...)
		$html = preg_replace_callback(
			'/(?<![\w-])url\((&quot;|["\']?)([^)"\'&]+)\1\)/',
			function ($m) use ($self, $dir, $mode) {
				return 'url('.$m[1].$self->map_url($m[2], $dir, $mode).$m[1].')';
			},
			$html
		);

		// URL absolut dalam JSON (konfigurasi JS tema/Elementor/plugin): https:\/\/stmi.ac.id\/...
		$html = preg_replace_callback(
			'#https?:\\\\/\\\\/(?:www\.)?stmi\.ac\.id(?![\w.-])((?:\\\\/[^\\\\"\'\s&<>]*)*)#',
			function ($m) use ($self, $mode) {
				$target = $self->map_absolute(str_replace('\\/', '/', $m[1]), $mode, TRUE);
				return ($target === NULL) ? $m[0] : $target;
			},
			$html
		);

		// URL absolut yang di-urlencode (link share media sosial): https%3A%2F%2Fstmi.ac.id%2F<path>
		$html = preg_replace_callback(
			'#https?%3A%2F%2F(?:www\.)?stmi\.ac\.id%2F([A-Za-z0-9%._~-]*)#',
			function ($m) use ($self, $mode) {
				if ($self->map_absolute('/'.rawurldecode($m[1]), 'literal') === NULL)
				{
					return $m[0];
				}
				if ($mode === 'php')
				{
					return '<?= rawurlencode(base_url()) ?>'.$m[1];
				}

				return ($mode === 'token' ? '{base_url_encoded}' : rawurlencode(base_url())).$m[1];
			},
			$html
		);

		return $html;
	}

	/**
	 * Petakan satu URL. URL eksternal, anchor, mailto, dsb. dibiarkan.
	 */
	public function map_url($url, $dir, $mode)
	{
		if ($url === '' OR preg_match('#^(\#|//|[a-z][a-z0-9+.-]*:)#i', $url))
		{
			if (preg_match('#^'.$this->origin.'(/.*)?$#i', $url, $m))
			{
				$target = $this->map_absolute(isset($m[1]) ? $m[1] : '', $mode);
				if ($target !== NULL)
				{
					return $target;
				}
			}

			return $url;
		}

		$rest = '';
		if (preg_match('/^([^?#]*)([?#].*)$/', $url, $m))
		{
			$url = $m[1];
			$rest = $m[2];
		}

		$path = $this->resolve($dir.$url);
		if ($path === NULL)
		{
			return $url.$rest;
		}

		if (preg_match('#^wp-(content|includes)/#', $path))
		{
			return $this->emit('base', $path.$rest, $mode);
		}

		// Varian HTTrack untuk URL ber-query (?p=ID, ?wpdmdl=ID): ikuti link canonical di file tersebut,
		// atau (jika tidak ada, mis. respons download) path asli dari komentar "Mirrored from"; query dari link dipertahankan.
		if (preg_match('#(^|/)index[0-9a-f]{4}(-\d+)?\.html$#', $path) && is_file($this->root.$path))
		{
			$variant = file_get_contents($this->root.$path);
			if (preg_match('/<link rel="canonical" href="([^"]*)"/', $variant, $c))
			{
				$target = $this->resolve(dirname($path) === '.' ? $c[1] : dirname($path).'/'.$c[1]);
				if ($target !== NULL)
				{
					$path = $target;
					$rest = preg_replace('/^\?[^#]*/', '', $rest);
				}
			}
			elseif (ltrim($variant) === '' OR ltrim($variant)[0] !== '<')
			{
				// Isi file hasil download (mis. PDF) disimpan HTTrack dengan nama index<hash>.html: URL-nya = folder paket.
				$path = dirname($path);
			}
			elseif (preg_match('#<META HTTP-EQUIV="Refresh" CONTENT="0; URL=(?![a-z]+:|//)([^"]+)"#i', $variant, $r)
				&& ($target = $this->resolve(dirname($path) === '.' ? $r[1] : dirname($path).'/'.$r[1])) !== NULL
				&& ! preg_match('#^wp-(content|includes)/#', $target))
			{
				// Halaman "Page has moved" HTTrack yang menunjuk ke halaman internal: ikuti redirect-nya.
				$path = $target;
				$rest = preg_replace('/^\?[^#]*/', '', $rest);
			}
			elseif (preg_match('#<!-- Mirrored from https?://(?:www\.)?stmi\.ac\.id/([^ ?]*?)/?(?:\?[^ ]*)? by HTTrack#', $variant, $mir))
			{
				// Redirect ke luar (mis. respons download): pakai URL asli halaman; query dari link dipertahankan.
				$path = $mir[1];
			}
		}

		// Link unduh Download Manager ke varian yang tidak tersimpan HTTrack: URL-nya = folder paket.
		if (preg_match('#^download/[^/]+/index[0-9a-f]{4}(-\d+)?\.html$#', $path) && ! is_file($this->root.$path) && strpos($rest, 'wpdmdl=') !== FALSE)
		{
			$path = dirname($path);
		}

		$path = preg_replace('#(^|/)index\.html$#', '', $path);
		$path = $this->real_path(rtrim($path, '/'));

		return $this->emit('site', $path.$rest, $mode);
	}

	/**
	 * Slug asli dari folder yang namanya dipotong HTTrack.
	 */
	public function truncated()
	{
		static $list = NULL;
		if ($list === NULL)
		{
			$list = array();
			foreach (glob($this->root.'*/index.html') as $file)
			{
				$folder = basename(dirname($file));
				$real = $this->real_path($folder);
				if ($real !== $folder)
				{
					$list[] = $real;
				}
			}
		}

		return $list;
	}

	/**
	 * Path asli WordPress untuk folder clone. HTTrack memotong nama folder yang terlalu panjang;
	 * URL aslinya ada di komentar "Mirrored from" di index.html folder tersebut.
	 */
	public function real_path($path)
	{
		static $cache = array();
		if ($path === '' OR ! is_file($this->root.$path.'/index.html'))
		{
			return $path;
		}
		if ( ! isset($cache[$path]))
		{
			$head = file_get_contents($this->root.$path.'/index.html', FALSE, NULL, 0, 1000);
			$cache[$path] = preg_match('#<!-- Mirrored from https?://(?:www\.)?stmi\.ac\.id/([^ ?]*?)/? by HTTrack#', $head, $m) && $m[1] !== ''
				? $m[1] : $path;
		}

		return $cache[$path];
	}

	/**
	 * Petakan path dari URL absolut https://stmi.ac.id/<path>. NULL = biarkan URL aslinya.
	 *
	 * - wp-content/wp-includes : ke base_url() hanya jika file/folder-nya ada di lokal
	 *                            (folder = base chunk JS di konfigurasi tema/Elementor/PDF Embedder).
	 * - halaman                : ke site_url() hanya jika halamannya ada di clone (<path>/index.html).
	 * - halaman depan          : ke site_url(), kecuali ber-query (?p=, ?s=).
	 * - endpoint WordPress (wp-admin, wp-json, feed, ...) dan path lain: dibiarkan.
	 */
	public function map_absolute($path, $mode, $json = FALSE)
	{
		$rest = '';
		if (preg_match('/^([^?#]*)([?#].*)$/', $path, $m))
		{
			$path = $m[1];
			$rest = $m[2];
		}
		$original = $path;
		$path = trim($path, '/');

		if (preg_match('#^wp-(content|includes)/#', $path))
		{
			$local = FCPATH.rawurldecode($path);
			if ( ! is_file($local) && ! is_dir($local))
			{
				return NULL;
			}

			// Pertahankan trailing slash pada base folder (mis. public_url: .../static/bundle/).
			$slash = (substr($path, -1) !== '/' && is_dir($local) && preg_match('#/$#', $original)) ? '/' : '';

			return $this->emit('base', $path.$slash.$rest, $mode, $json);
		}

		if ($path === '')
		{
			return ($rest !== '' && $rest[0] === '?') ? NULL : $this->emit('site', $rest, $mode, $json);
		}

		if (preg_match($this->wp_only, $path) OR ( ! is_file($this->root.$path.'/index.html') && ! in_array($path, $this->truncated(), TRUE)))
		{
			return NULL;
		}

		return $this->emit('site', $path.$rest, $mode, $json);
	}

	protected function emit($type, $path, $mode, $json = FALSE)
	{
		$fn = $type.'_url'.($json ? '_json' : '');

		// Mode 'token': untuk konten di database. site_url() = base_url() karena index_page kosong.
		if ($mode === 'token')
		{
			return ($json ? '{base_url_json}'.str_replace('/', '\\/', $path) : '{base_url}'.$path);
		}

		if ($mode === 'php')
		{
			return '<?= '.$fn."('".addcslashes($path, "'\\")."') ?>";
		}

		return $fn($path);
	}

	/**
	 * Normalisasi path relatif (../, ./). NULL jika keluar dari root clone.
	 */
	protected function resolve($path)
	{
		$out = array();
		foreach (explode('/', $path) as $seg)
		{
			if ($seg === '' OR $seg === '.')
			{
				continue;
			}
			if ($seg === '..')
			{
				if (empty($out))
				{
					return NULL;
				}
				array_pop($out);
				continue;
			}
			$out[] = $seg;
		}

		return implode('/', $out);
	}

	/**
	 * Pecah halaman (sudah di-clean & rewrite) menjadi bagian-bagian layout.
	 *
	 * document_open : awal dokumen s.d. <title>
	 * title         : isi <title>
	 * head          : </title> s.d. sebelum <body   (aset CSS/JS per template)
	 * body_attrs    : atribut tag <body>
	 * drawer        : setelah <body ...> s.d. sebelum <header id="header"   (termasuk menu mobile)
	 * header        : <header id="header"> ... </header>
	 * content       : setelah </header> s.d. sebelum <footer id="footer"
	 * footer        : <footer id="footer"> ... </footer>
	 * foot          : setelah </footer> s.d. akhir dokumen (script per template)
	 */
	public function split($html)
	{
		$p = array();
		$p['title_start'] = $this->pos($html, '<title>') + strlen('<title>');
		$p['title_end'] = $this->pos($html, '</title>', $p['title_start']);
		$p['body'] = $this->pos($html, '<body', $p['title_end']);
		$p['body_end'] = $this->pos($html, '>', $p['body']);
		$p['header'] = $this->pos($html, '<header id="header"', $p['body_end']);
		$p['header_end'] = $this->pos($html, '</header>', $p['header']) + strlen('</header>');
		$p['footer'] = $this->pos($html, '<footer id="footer"', $p['header_end']);
		$p['footer_end'] = $this->pos($html, '</footer>', $p['footer']) + strlen('</footer>');

		return array(
			'document_open' => substr($html, 0, $p['title_start']),
			'title'         => substr($html, $p['title_start'], $p['title_end'] - $p['title_start']),
			'head'          => substr($html, $p['title_end'], $p['body'] - $p['title_end']),
			'body_attrs'    => substr($html, $p['body'] + 5, $p['body_end'] - $p['body'] - 5),
			'drawer'        => substr($html, $p['body_end'] + 1, $p['header'] - $p['body_end'] - 1),
			'header'        => substr($html, $p['header'], $p['header_end'] - $p['header']),
			'content'       => substr($html, $p['header_end'], $p['footer'] - $p['header_end']),
			'footer'        => substr($html, $p['footer'], $p['footer_end'] - $p['footer']),
			'foot'          => substr($html, $p['footer_end']),
		);
	}

	protected function pos($html, $needle, $offset = 0)
	{
		$pos = strpos($html, $needle, $offset);
		if ($pos === FALSE)
		{
			throw new RuntimeException('Penanda "'.$needle.'" tidak ditemukan; halaman ini bukan layout standar.');
		}

		return $pos;
	}

	/**
	 * Hapus penanda menu aktif dari markup menu dan kembalikan daftar kelasnya per ID menu.
	 * Kebalikan dari wp_menu_active() di wp_helper.
	 */
	public function neutralize_menu($html, array &$active)
	{
		$html = preg_replace_callback(
			'/(<li\b[^>]*?class=")([^"]*\bmenu-item-(\d+)\b[^"]*)(")/',
			function ($m) use (&$active) {
				$removed = array();
				$class = preg_replace_callback(
					'/ (current[-_][a-z_-]+|page_item|page-item-\d+)(?=[ ]|$)/',
					function ($t) use (&$removed) {
						$removed[] = $t[1];
						return '';
					},
					$m[2]
				);

				if ( ! empty($removed))
				{
					$extra = implode(' ', $removed);
					if (isset($active[$m[3]]) && $active[$m[3]] !== $extra)
					{
						throw new RuntimeException('Kelas menu aktif tidak konsisten untuk menu-item-'.$m[3]);
					}
					$active[$m[3]] = $extra;
				}

				return $m[1].$class.$m[4];
			},
			$html
		);

		return str_replace(' aria-current="page"', '', $html);
	}

	/**
	 * WordPress menambah fetchpriority="high" / loading="lazy" pada <img> tergantung isi halaman.
	 * Atribut itu diganti placeholder wp_img_hint() dan nilainya disimpan per gambar ("bagian:urutan").
	 */
	public function neutralize_img_hints($html, $part, array &$hints)
	{
		$i = 0;

		return preg_replace_callback(
			'/<img ((?:fetchpriority="[^"]*" |loading="[^"]*" )*)/',
			function ($m) use ($part, &$i, &$hints) {
				$key = $part.':'.$i++;
				if ($m[1] !== '')
				{
					$hints[$key] = $m[1];
				}

				return "<img <?= wp_img_hint(\$img_hints, '".$key."') ?>";
			},
			$html
		);
	}

	/**
	 * Logo KAN di footer: di beberapa halaman WordPress menambah kelas wp-post-image.
	 */
	public function neutralize_footer($html, array &$params)
	{
		$class = 'class="image wp-image-612  attachment-full size-full';
		$params['footer_logo_post_image'] = (strpos($html, $class.' wp-post-image"') !== FALSE);

		return str_replace(
			array($class.' wp-post-image"', $class.'"'),
			$class.'<?= $footer_logo_post_image ? \' wp-post-image\' : \'\' ?>"',
			$html
		);
	}

	/**
	 * Menu utama (header desktop & mobile) diganti pemanggilan wp_nav_menu(), sama seperti di partial.
	 */
	public function neutralize_main_menu($html)
	{
		$html = preg_replace('#<ul id="menu-menu-utama" class="menu">.*?</ul>(?=</nav>)#s', '<?= wp_nav_menu($main_menu, FALSE) ?>', $html);

		return preg_replace('#<ul id="menu-menu-utama-1" class="">.*?</ul>(?=</nav>)#s', '<?= wp_nav_menu($main_menu, TRUE) ?>', $html);
	}

	/**
	 * Daftar link footer (menu-footer-menu) diganti loop $footer_links, sama seperti di partial footer.
	 */
	public function neutralize_footer_links($html)
	{
		$loop = '<ul id="menu-footer-menu" class="widget-menu"><?php foreach ($footer_links as $i => $link): ?>'
			.'<li id="menu-item-<?= 619 + $i ?>" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-<?= 619 + $i ?>">'
			.'<a href="<?= html_escape($link[\'url\']) ?>"><?= html_escape($link[\'title\']) ?></a></li>'."\n"
			.'<?php endforeach; ?></ul>';

		return preg_replace_callback('#<ul id="menu-footer-menu" class="widget-menu">.*?</ul>#s', function () use ($loop) {
			return $loop;
		}, $html);
	}

	public function neutralize_contacts($html)
	{
		// PHP menelan newline setelah tag penutup, jadi newline aslinya dicetak lewat . "\n" (sama seperti di partial).
		$html = str_replace("humas@stmi.ac.id\n", "<?= html_escape(\$contacts['email']) . \"\\n\" ?>\n", $html);
		$html = str_replace("021-42888206\n", "<?= html_escape(\$contacts['phone']) . \"\\n\" ?>\n", $html);
		$html = str_replace("0851-552-44455 \n", "<?= html_escape(\$contacts['whatsapp']) ?> \n", $html);
		$html = str_replace("0851-552-44455\n", "<?= html_escape(\$contacts['whatsapp']) . \"\\n\" ?>\n", $html);
		$html = str_replace('https:\/\/web.whatsapp.com\/send?phone=6285155244455', '<?= str_replace(\'/\', \'\\/\', html_escape($contacts[\'whatsapp_url\'])) ?>', $html);
		$html = str_replace('"value":"6285155244455"', '"value":"<?= html_escape(preg_replace(\'/[^0-9]/\', \'\', strpos($contacts[\'whatsapp\'], \'0\') === 0 ? \'62\' . substr($contacts[\'whatsapp\'], 1) : $contacts[\'whatsapp\'])) ?>"', $html);
		
		$html = str_replace('https://twitter.com/stmijakarta?lang=en', '<?= html_escape($contacts[\'social_twitter\']) ?>', $html);
		$html = str_replace('https://www.instagram.com/stmijakarta/?hl=en', '<?= html_escape($contacts[\'social_instagram\']) ?>', $html);
		$html = str_replace('https://www.facebook.com/PoliteknikSTMIJakarta', '<?= html_escape($contacts[\'social_facebook\']) ?>', $html);
		$html = str_replace('https://www.youtube.com/channel/UCFalakPYmXniFeqHapt1k8w', '<?= html_escape($contacts[\'social_youtube\']) ?>', $html);
		
		return $html;
	}
}
