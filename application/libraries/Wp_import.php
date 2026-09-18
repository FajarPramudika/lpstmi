<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Impor post WordPress dari clone ke database.
 *
 * Sumber data:
 *  - stmi.ac.id-clone/wp-json/wp/v2/{posts,users,categories,tags}/*.json  (data mentah WordPress)
 *  - REST API situs live untuk post yang JSON-nya tidak ikut ter-clone dan metadata gambar (media),
 *    disimpan di application/cache/wp-api/ supaya impor bisa diulang tanpa internet.
 *  - HTML clone (<slug>/index.html) untuk isi yang harus identik: konten, urutan kategori/tag,
 *    dan excerpt di kartu arsip.
 */
class Wp_import {

	protected $CI;
	protected $clone;
	protected $cache;
	protected $api = 'https://stmi.ac.id/wp-json/wp/v2/';
	protected $log = array();

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('wp_clone');
		$this->clone = $this->CI->wp_clone;
		$this->cache = APPPATH.'cache/wp-api/';
	}

	public function log()
	{
		return $this->log;
	}

	/**
	 * Jalankan impor. Isi tabel konten dikosongkan dulu, jadi aman diulang.
	 */
	public function run()
	{
		$root = $this->clone->root();
		$pages = $this->post_pages();
		$excerpts = $this->archive_excerpts();
		$nav = $this->nav_data();

		$authors = $this->json_dir($root.'wp-json/wp/v2/users');
		$categories = $this->json_dir($root.'wp-json/wp/v2/categories');
		$tags = $this->json_dir($root.'wp-json/wp/v2/tags');

		$rows = array('authors' => array(), 'terms' => array(), 'media' => array(), 'posts' => array(), 'post_terms' => array());

		foreach ($authors as $a)
		{
			$rows['authors'][] = array_merge(
				array('id' => $a['id'], 'slug' => $a['slug'], 'display_name' => $a['name'], 'website' => $a['url'] !== '' ? $a['url'] : NULL),
				$this->author_profile($a['slug'])
			);
		}
		foreach (array('category' => $categories, 'post_tag' => $tags) as $taxonomy => $terms)
		{
			foreach ($terms as $t)
			{
				$rows['terms'][] = array(
					'id'          => $t['id'],
					'taxonomy'    => $taxonomy,
					'name'        => html_entity_decode($t['name'], ENT_QUOTES, 'UTF-8'),
					'slug'        => $t['slug'],
					'description' => $t['description'],
				);
			}
		}

		$media_ids = array();
		foreach ($pages as $id => $rel)
		{
			$json = $this->post_json($id);
			$html = $this->clone->rewrite_urls($this->clone->clean($this->clone->read($rel)), $rel, 'token');
			$data = $this->parse_single($html, $rel);

			if ( ! isset($excerpts[$id]))
			{
				throw new RuntimeException('Excerpt post '.$id.' tidak ditemukan di halaman arsip.');
			}

			$rows['posts'][] = array(
				'id'                => $id,
				'slug'              => $json['slug'],
				'title'             => $this->raw_title($json['slug'], $data['title'], $nav),
				'content'           => $data['content'],
				'excerpt'           => $excerpts[$id],
				'author_id'         => $json['author'],
				'featured_media_id' => $json['featured_media'] ? $json['featured_media'] : NULL,
				'status'            => 'publish',
				'published_at'      => str_replace('T', ' ', $json['date']),
				'modified_at'       => str_replace('T', ' ', $json['modified']),
				'layout_head'       => NULL,
				'layout_foot'       => NULL,
			);

			// Post Elementor punya CSS/JS sendiri: varian layout post-<ID> (dibuat dengan tools layout).
			if (strpos($data['content'], 'data-elementor-type=') !== FALSE)
			{
				$variant = 'post-'.$id;
				$n = count($rows['posts']) - 1;
				foreach (array('head', 'foot') as $type)
				{
					if ( ! is_file(APPPATH.'views/layouts/'.$type.'/'.$variant.'.php'))
					{
						throw new RuntimeException('Post Elementor '.$id.' butuh varian layouts/'.$type.'/'.$variant.'.php (php index.php tools layout '.$variant.' '.$rel.')');
					}
					$rows['posts'][$n]['layout_'.$type] = $variant;
				}
			}

			$order = 0;
			foreach (array_merge($data['categories'], $data['tags']) as $term_id)
			{
				$rows['post_terms'][] = array('post_id' => $id, 'term_id' => $term_id, 'term_order' => $order++);
			}

			if ($json['featured_media'])
			{
				$media_ids[$json['featured_media']] = array($id, $json['slug']);
			}
		}

		$cards = $this->archive_card_images();
		foreach ($media_ids as $media_id => $owner)
		{
			list($post_id, $post_slug) = $owner;
			try
			{
				$rows['media'][] = $this->media_row($media_id);
			}
			catch (RuntimeException $e)
			{
				if ( ! isset($cards[$post_id]))
				{
					throw $e;
				}
				$rows['media'][] = $this->media_from_card($media_id, $cards[$post_id], isset($nav['img'][$post_slug]) ? $nav['img'][$post_slug] : NULL);
				$this->log[] = 'media '.$media_id.' tidak bisa diakses di API live; diambil dari kartu arsip clone';
			}
		}

		$db = $this->CI->db;
		$db->trans_start();
		$db->query('SET FOREIGN_KEY_CHECKS = 0');
		foreach (array('post_terms', 'posts', 'media', 'terms') as $table)
		{
			$db->query('DELETE FROM '.$table);
		}
		// Author: pertahankan data login yang sudah diisi, hanya perbarui slug/nama.
		foreach ($rows['authors'] as $a)
		{
			$db->query('INSERT INTO authors (id, slug, display_name, registered_at, gravatar_hash, website) VALUES (?, ?, ?, ?, ?, ?)
				ON DUPLICATE KEY UPDATE slug = VALUES(slug), display_name = VALUES(display_name),
				registered_at = VALUES(registered_at), gravatar_hash = VALUES(gravatar_hash), website = VALUES(website)',
				array($a['id'], $a['slug'], $a['display_name'], $a['registered_at'], $a['gravatar_hash'], $a['website']));
		}
		foreach (array('terms', 'media', 'posts', 'post_terms') as $table)
		{
			if ( ! empty($rows[$table]))
			{
				$db->insert_batch($table, $rows[$table]);
			}
		}
		// Articles: di WordPress dihitung dari semua tipe konten; simpan selisihnya terhadap post di database.
		foreach ($rows['authors'] as $a)
		{
			$db->query('UPDATE authors SET post_count_offset = ? - (SELECT COUNT(*) FROM posts WHERE author_id = ? AND status = \'publish\') WHERE id = ?',
				array($a['articles'], $a['id'], $a['id']));
		}
		$db->query('SET FOREIGN_KEY_CHECKS = 1');
		$db->trans_complete();

		if ($db->trans_status() === FALSE)
		{
			throw new RuntimeException('Transaksi impor gagal.');
		}

		foreach (array('authors', 'terms', 'media', 'posts', 'post_terms') as $table)
		{
			$this->log[] = str_pad($table, 11).count($rows[$table]).' baris';
		}

		return $pages;
	}

	/**
	 * Semua halaman single post di clone: ID => path relatif.
	 */
	public function post_pages()
	{
		$root = $this->clone->root();
		$pages = array();
		foreach (glob($root.'*/index.html') as $file)
		{
			$head = file_get_contents($file, FALSE, NULL, 0, 200000);
			if (preg_match('/<body class="[^"]*\bsingle-post\b[^"]*\bpostid-(\d+)\b/', $head, $m))
			{
				$pages[(int) $m[1]] = substr($file, strlen($root));
			}
		}
		ksort($pages);

		return $pages;
	}

	/**
	 * Ambil judul, konten, dan urutan kategori/tag dari halaman single post (HTML mode token).
	 */
	protected function parse_single($html, $rel)
	{
		if ( ! preg_match('/<h1 class="page-title" itemprop="headline">(.*?)<\/h1>/s', $html, $t))
		{
			throw new RuntimeException('Judul tidak ditemukan: '.$rel);
		}

		$open = '<div class="entry-content is-layout-flow">'."\n\t\t\t";
		$start = strpos($html, $open);
		$share = strpos($html, '<div class="ct-share-box', $start);
		// Template: <div class="entry-content ...">\n\t\t\t{the_content}\t\t</div>
		$close = strrpos(substr($html, 0, $share), "\t\t</div>\n\n\t\t\n");
		if ($start === FALSE OR $share === FALSE OR $close === FALSE)
		{
			throw new RuntimeException('Konten tidak ditemukan: '.$rel);
		}
		$start += strlen($open);

		$categories = array();
		if (preg_match('/<li class="meta-categories" data-type="simple">.*?<\/li>/s', $html, $m))
		{
			preg_match_all('/class="ct-term-(\d+)"/', $m[0], $ids);
			$categories = array_map('intval', $ids[1]);
		}

		$tags = array();
		if (preg_match('/<div class="entry-tags-items">(.*?)<\/div>/s', $html, $m))
		{
			preg_match_all('/href="\{base_url\}tag\/([^"]+)"/', $m[1], $slugs);
			foreach ($slugs[1] as $slug)
			{
				$tags[] = $this->term_id('post_tag', $slug);
			}
		}

		return array(
			'title'      => $t[1],
			'content'    => substr($html, $start, $close - $start),
			'categories' => $categories,
			'tags'       => $tags,
		);
	}

	/**
	 * Excerpt dari kartu di halaman arsip kategori: ID post => HTML excerpt.
	 */
	protected function archive_excerpts()
	{
		$root = $this->clone->root();
		$files = array_merge(glob($root.'category/*/index.html'), glob($root.'category/*/page/*/index.html'));
		$out = array();

		foreach ($files as $file)
		{
			$html = file_get_contents($file);
			preg_match_all('/<article class="entry-card post-(\d+) .*?<div class="entry-excerpt">(.*?)<\/div>/s', $html, $m, PREG_SET_ORDER);
			foreach ($m as $card)
			{
				$out[(int) $card[1]] = $card[2];
			}
		}

		return $out;
	}

	/**
	 * Dari navigasi Previous/Next di semua halaman post: judul mentah dan <img> (ukuran medium) per slug.
	 */
	protected function nav_data()
	{
		$out = array('title' => array(), 'img' => array());
		foreach ($this->post_pages() as $rel)
		{
			$html = $this->clone->read($rel);
			preg_match_all('#<a href="\.\./([^/"]+)/index\.html" class="nav-item-(?:prev|next)">(.*?)</a>#s', $html, $m, PREG_SET_ORDER);
			foreach ($m as $item)
			{
				$item[1] = $this->clone->real_path($item[1]);
				if (preg_match('#<span class="item-title ct-hidden-sm">\n\t{8}(.*?)\t{7}</span>#s', $item[2], $t))
				{
					$out['title'][$item[1]] = html_entity_decode($t[1], ENT_QUOTES, 'UTF-8');
				}
				if (preg_match('#<img [^>]*/>#', $item[2], $img))
				{
					$out['img'][$item[1]] = $img[0];
				}
			}
		}

		return $out;
	}

	/**
	 * Judul mentah dari navigasi; harus menghasilkan judul yang sama persis setelah wp_texturize().
	 */
	protected function raw_title($slug, $rendered, array $nav)
	{
		if ( ! isset($nav['title'][$slug]))
		{
			throw new RuntimeException('Judul mentah post '.$slug.' tidak ditemukan di navigasi.');
		}

		$raw = $nav['title'][$slug];
		$this->CI->load->helper('wp');
		if (wp_texturize($raw) !== $rendered)
		{
			throw new RuntimeException('wp_texturize() tidak cocok untuk '.$slug.': '.$rendered);
		}

		return $raw;
	}

	/**
	 * Data author dari halaman arsip author di clone: tanggal join, avatar, jumlah Articles.
	 */
	protected function author_profile($slug)
	{
		$html = $this->clone->read('author/'.$slug.'/index.html');
		if ( ! preg_match('#Joined:&nbsp;(\d{2})/(\d{2})/(\d{4})#', $html, $j)
			OR ! preg_match('#Articles:&nbsp;(\d+)#', $html, $n)
			OR ! preg_match('#gravatar\.com/avatar/([0-9a-f]{64})#', $html, $g))
		{
			throw new RuntimeException('Profil author '.$slug.' tidak lengkap di clone.');
		}

		return array(
			'registered_at' => $j[3].'-'.$j[2].'-'.$j[1].' 00:00:00',
			'gravatar_hash' => $g[1],
			'articles'      => (int) $n[1],
		);
	}

	/**
	 * <img> di kartu arsip (ukuran medium_large): ID post => atribut img.
	 */
	protected function archive_card_images()
	{
		$root = $this->clone->root();
		$files = array_merge(glob($root.'category/*/index.html'), glob($root.'category/*/page/*/index.html'));
		$out = array();

		foreach ($files as $file)
		{
			$html = file_get_contents($file);
			preg_match_all('/<article class="entry-card post-(\d+) [^"]*" ><a [^>]*><img ([^>]*)>/', $html, $m, PREG_SET_ORDER);
			foreach ($m as $card)
			{
				$out[(int) $card[1]] = $card[2];
			}
		}

		return $out;
	}

	/**
	 * Susun data media dari <img> kartu arsip jika API live menolak (media privat).
	 * srcset kartu memuat semua ukuran dengan rasio sama, urut seperti metadata WordPress.
	 */
	protected function media_from_card($id, $img, $nav_img)
	{
		preg_match('/srcset="([^"]*)"/', $img, $srcset);
		preg_match('/ alt="([^"]*)"/', $img, $alt);

		// Urutan metadata: srcset kartu = [medium_large, lainnya urut metadata]; srcset navigasi = [medium, lainnya].
		// Sisipkan medium_large setelah pendahulunya di srcset navigasi.
		$strip = function ($list) {
			return array_map(function ($x) { return preg_replace('#^(\.\./)+#', '', trim($x)); }, $list);
		};
		$order = $strip(explode(', ', $srcset[1]));
		if ($nav_img && preg_match('/srcset="([^"]*)"/', $nav_img, $nav_srcset))
		{
			$first = array_shift($order);
			$nav_order = array_slice($strip(explode(', ', $nav_srcset[1])), 1);
			$pos = array_search($first, $nav_order);
			if ($pos !== FALSE)
			{
				$before = ($pos > 0) ? array_search($nav_order[$pos - 1], $order) : -1;
				array_splice($order, $before === FALSE ? 0 : $before + 1, 0, array($first));
			}
			else
			{
				array_unshift($order, $first);
			}
		}

		$full = NULL;
		$sizes = array();
		foreach ($order as $source)
		{
			list($url, $w) = explode(' ', trim($source));
			$path = preg_replace('#^(\.\./)*wp-content/uploads/#', '', $url);
			$w = (int) $w;
			if (preg_match('/-(\d+)x(\d+)\.[a-z]+$/i', $path, $d))
			{
				$sizes[$this->size_name((int) $d[1], (int) $d[2])] = array('file' => basename($path), 'width' => (int) $d[1], 'height' => (int) $d[2]);
			}
			else
			{
				$full = array('file' => $path, 'width' => $w);
			}
		}

		$ref = reset($sizes);
		$ext = strtolower(pathinfo($full['file'], PATHINFO_EXTENSION));

		return array(
			'id'         => $id,
			'file'       => $full['file'],
			'width'      => $full['width'],
			'height'     => (int) round($full['width'] * $ref['height'] / $ref['width']),
			'alt'        => isset($alt[1]) ? html_entity_decode($alt[1], ENT_QUOTES, 'UTF-8') : '',
			'mime_type'  => ($ext === 'png') ? 'image/png' : (($ext === 'webp') ? 'image/webp' : 'image/jpeg'),
			'sizes'      => json_encode($sizes),
			'created_at' => date('Y-m-d H:i:s'),
		);
	}

	/**
	 * Nama ukuran WordPress dari dimensinya.
	 */
	protected function size_name($w, $h)
	{
		$max = max($w, $h);
		if ($w === 150 && $h === 150) return 'thumbnail';
		if ($max <= 300) return 'medium';
		if ($w === 768) return 'medium_large';
		if ($max <= 1024) return 'large';
		if ($max <= 1536) return '1536x1536';
		return '2048x2048';
	}

	protected function term_id($taxonomy, $slug)
	{
		static $map = NULL;
		if ($map === NULL)
		{
			$map = array();
			$dirs = array('category' => 'categories', 'post_tag' => 'tags');
			foreach ($dirs as $tax => $dir)
			{
				foreach ($this->json_dir($this->clone->root().'wp-json/wp/v2/'.$dir) as $t)
				{
					$map[$tax][$t['slug']] = (int) $t['id'];
				}
			}
		}

		if ( ! isset($map[$taxonomy][$slug]))
		{
			throw new RuntimeException('Term tidak dikenal: '.$taxonomy.'/'.$slug);
		}

		return $map[$taxonomy][$slug];
	}

	protected function post_json($id)
	{
		$file = $this->clone->root().'wp-json/wp/v2/posts/'.$id.'.json';
		if (is_file($file))
		{
			return json_decode(file_get_contents($file), TRUE);
		}

		return $this->fetch('posts/'.$id);
	}

	protected function media_row($id)
	{
		$m = $this->fetch('media/'.$id);
		$details = $m['media_details'];
		$sizes = array();

		foreach ($details['sizes'] as $name => $size)
		{
			if ($name === 'full')
			{
				continue;
			}
			$sizes[$name] = array('file' => $size['file'], 'width' => (int) $size['width'], 'height' => (int) $size['height']);
		}

		$file = $details['file'];
		foreach (array_merge(array($file), array_map(function ($s) use ($file) {
			return dirname($file).'/'.$s['file'];
		}, $sizes)) as $path)
		{
			if ( ! is_file(FCPATH.'wp-content/uploads/'.$path))
			{
				$this->log[] = 'gambar tidak ada di lokal: wp-content/uploads/'.$path;
			}
		}

		return array(
			'id'         => $id,
			'file'       => $file,
			'width'      => (int) $details['width'],
			'height'     => (int) $details['height'],
			'alt'        => $m['alt_text'],
			'mime_type'  => $m['mime_type'],
			'sizes'      => json_encode($sizes),
			'created_at' => str_replace('T', ' ', $m['date']),
		);
	}

	/**
	 * GET ke REST API live, dengan cache di application/cache/wp-api/.
	 */
	protected function fetch($path)
	{
		$file = $this->cache.$path.'.json';
		if ( ! is_file($file))
		{
			$body = @file_get_contents($this->api.$path);
			if ($body === FALSE OR json_decode($body) === NULL)
			{
				throw new RuntimeException('Gagal mengambil '.$this->api.$path);
			}
			if ( ! is_dir(dirname($file)))
			{
				mkdir(dirname($file), 0775, TRUE);
			}
			file_put_contents($file, $body);
			$this->log[] = 'ambil dari live: '.$path;
		}

		return json_decode(file_get_contents($file), TRUE);
	}

	protected function json_dir($dir)
	{
		$out = array();
		foreach (glob($dir.'/*.json') as $file)
		{
			$out[] = json_decode(file_get_contents($file), TRUE);
		}

		return $out;
	}
}
