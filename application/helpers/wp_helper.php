<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('base_url_json'))
{
	/**
	 * base_url() dengan slash di-escape, untuk URL di dalam JSON konfigurasi JS
	 * (format WordPress: https:\/\/domain\/).
	 */
	function base_url_json($uri = '')
	{
		return str_replace('/', '\/', base_url($uri));
	}
}

if ( ! function_exists('site_url_json'))
{
	/**
	 * site_url() dengan slash di-escape, untuk URL di dalam JSON.
	 */
	function site_url_json($uri = '')
	{
		return str_replace('/', '\/', site_url($uri));
	}
}

if ( ! function_exists('wp_menu_active'))
{
	/**
	 * Tandai menu aktif persis seperti output WordPress.
	 *
	 * $active = array(ID menu-item => 'kelas tambahan'), contoh:
	 *   array(1816 => 'current-menu-item page_item page-item-778 current_page_item',
	 *         1795 => 'current-menu-ancestor current-menu-parent')
	 *
	 * Kelas disisipkan sebelum "menu-item-has-children" (jika ada) atau sebelum "menu-item-{ID}".
	 * Item dengan "current-menu-item" juga mendapat aria-current="page" pada <a> pertamanya.
	 */
	function wp_menu_active($html, array $active)
	{
		if (empty($active))
		{
			return $html;
		}
		
		return preg_replace_callback(
			'/(<li\b[^>]*?class=")([^"]*\bmenu-item-(\d+)\b[^"]*)("[^>]*>(?:\s*<a href="[^"]*")?)/',
			function ($m) use ($active) {
				if ( ! isset($active[$m[3]]))
				{
					return $m[0];
				}

				$extra = $active[$m[3]];
				$class = $m[2];
				$anchor = ' menu-item-has-children';
				if (strpos($class, $anchor) === FALSE)
				{
					$anchor = ' menu-item-'.$m[3];
				}

				$pos = strrpos($class, $anchor);
				$class = substr($class, 0, $pos).' '.$extra.substr($class, $pos);

				$tail = $m[4];
				if (preg_match('/(^| )current-menu-item( |$)/', $extra))
				{
					$tail .= ' aria-current="page"';
				}

				return $m[1].$class.$tail;
			},
			$html
		);
	}
}

if ( ! function_exists('wp_img_hint'))
{
	/**
	 * Atribut fetchpriority/loading untuk <img> di layout bersama, sesuai halaman
	 * (WordPress menentukannya dari urutan gambar di tiap halaman).
	 */
	function wp_img_hint(array $hints, $key)
	{
		return isset($hints[$key]) ? $hints[$key] : '';
	}
}

if ( ! function_exists('wp_date'))
{
	/**
	 * Format tanggal dari kolom DATETIME (waktu lokal Asia/Jakarta), nama hari/bulan bahasa Inggris seperti WordPress.
	 */
	function wp_date($format, $datetime)
	{
		return date($format, strtotime($datetime));
	}
}

if ( ! function_exists('wp_content'))
{
	/**
	 * Ganti token URL di konten database ({base_url}, {base_url_json}) dengan URL situs.
	 */
	function wp_content($html)
	{
		return str_replace(
			array('{base_url_json}', '{base_url_encoded}', '{base_url}'),
			array(base_url_json(), rawurlencode(base_url()), base_url()),
			$html
		);
	}
}

if ( ! function_exists('wp_term_links'))
{
	/**
	 * Daftar link kategori seperti the_category(', '): <a href rel="tag" class="ct-term-ID">Nama</a>, ...
	 */
	function wp_term_links(array $terms, $taxonomy)
	{
		$base = ($taxonomy === 'category') ? 'category/' : 'tag/';
		$links = array();
		foreach ($terms as $t)
		{
			$links[] = '<a href="'.site_url($base.$t['slug']).'" rel="tag" class="ct-term-'.$t['id'].'">'.html_escape($t['name']).'</a>';
		}

		return implode(', ', $links);
	}
}

if ( ! function_exists('wp_post_class'))
{
	/**
	 * Kelas post seperti get_post_class(): post-ID post type-post status-publish format-standard
	 * [has-post-thumbnail] hentry category-* tag-*.
	 */
	function wp_post_class(array $post, array $categories, array $tags)
	{
		$classes = array('post-'.$post['id'], 'post', 'type-post', 'status-'.$post['status'], 'format-standard');
		if ( ! empty($post['featured_media_id']))
		{
			$classes[] = 'has-post-thumbnail';
		}
		$classes[] = 'hentry';
		foreach ($categories as $c)
		{
			$classes[] = 'category-'.$c['slug'];
		}
		foreach ($tags as $t)
		{
			$classes[] = 'tag-'.$t['slug'];
		}

		return implode(' ', $classes);
	}
}

if ( ! function_exists('wp_constrain_dimensions'))
{
	/** Port wp_constrain_dimensions() WordPress. */
	function wp_constrain_dimensions($current_width, $current_height, $max_width = 0, $max_height = 0)
	{
		if ( ! $max_width && ! $max_height)
		{
			return array($current_width, $current_height);
		}

		$width_ratio = $height_ratio = 1.0;
		$did_width = $did_height = FALSE;

		if ($max_width > 0 && $current_width > 0 && $current_width > $max_width)
		{
			$width_ratio = $max_width / $current_width;
			$did_width = TRUE;
		}
		if ($max_height > 0 && $current_height > 0 && $current_height > $max_height)
		{
			$height_ratio = $max_height / $current_height;
			$did_height = TRUE;
		}

		$smaller_ratio = min($width_ratio, $height_ratio);
		$larger_ratio = max($width_ratio, $height_ratio);

		if ((int) round($current_width * $larger_ratio) > $max_width OR (int) round($current_height * $larger_ratio) > $max_height)
		{
			$ratio = $smaller_ratio;
		}
		else
		{
			$ratio = $larger_ratio;
		}

		$w = max(1, (int) round($current_width * $ratio));
		$h = max(1, (int) round($current_height * $ratio));

		if ($did_width && $w === $max_width - 1)
		{
			$w = $max_width;
		}
		if ($did_height && $h === $max_height - 1)
		{
			$h = $max_height;
		}

		return array($w, $h);
	}
}

if ( ! function_exists('wp_image_matches_ratio'))
{
	/** Port wp_image_matches_ratio() WordPress. */
	function wp_image_matches_ratio($source_width, $source_height, $target_width, $target_height)
	{
		if ($source_width > $target_width)
		{
			$constrained = wp_constrain_dimensions($source_width, $source_height, $target_width);
			$expected = array($target_width, $target_height);
		}
		else
		{
			$constrained = wp_constrain_dimensions($target_width, $target_height, $source_width);
			$expected = array($source_width, $source_height);
		}

		return abs($constrained[0] - $expected[0]) <= 1 && abs($constrained[1] - $expected[1]) <= 1;
	}
}

if ( ! function_exists('wp_post_thumbnail'))
{
	/**
	 * Markup gambar unggulan seperti Blocksy/WordPress (loading lazy, srcset, sizes).
	 *
	 * $media : baris tabel media (sizes berupa JSON)
	 * $size  : 'medium' (navigasi post) atau 'medium_large' (kartu arsip)
	 * $ratio : isi style aspect-ratio (mis. '1/1', '4/3')
	 */
	function wp_post_thumbnail(array $media, $size, $ratio)
	{
		$sizes = json_decode($media['sizes'], TRUE);
		$dir = dirname($media['file']);
		$dir = ($dir === '.') ? '' : $dir.'/';

		if (isset($sizes[$size]))
		{
			$src = $sizes[$size];
			$src['file'] = $dir.$src['file'];
		}
		else
		{
			// Gambar lebih kecil dari ukuran yang diminta: WordPress memakai ukuran penuh.
			$src = array('file' => $media['file'], 'width' => (int) $media['width'], 'height' => (int) $media['height']);
		}

		// wp_calculate_image_srcset(): semua ukuran dengan rasio sama (maks. lebar 2048), urut metadata; src di depan.
		$candidates = array();
		foreach ($sizes as $s)
		{
			$candidates[] = array('file' => $dir.$s['file'], 'width' => (int) $s['width'], 'height' => (int) $s['height']);
		}
		$candidates[] = array('file' => $media['file'], 'width' => (int) $media['width'], 'height' => (int) $media['height']);

		$sources = array();
		$src_matched = FALSE;
		foreach ($candidates as $c)
		{
			$is_src = FALSE;
			if ( ! $src_matched && $c['file'] === $src['file'])
			{
				$src_matched = $is_src = TRUE;
			}
			if ($c['width'] > 2048 && ! $is_src)
			{
				continue;
			}
			if (wp_image_matches_ratio($src['width'], $src['height'], $c['width'], $c['height']))
			{
				$source = base_url('wp-content/uploads/'.$c['file']).' '.$c['width'].'w';
				if ($is_src)
				{
					$sources = array($c['width'] => $source) + $sources;
				}
				else
				{
					$sources[$c['width']] = $source;
				}
			}
		}

		$html = '<img loading="lazy" width="'.$src['width'].'" height="'.$src['height'].'"'
			.' src="'.base_url('wp-content/uploads/'.$src['file']).'"'
			.' class="attachment-'.$size.' size-'.$size.' wp-post-image" alt="'.html_escape($media['alt']).'" loading="lazy" decoding="async"';

		if ($src_matched && count($sources) > 1)
		{
			$html .= ' srcset="'.implode(', ', $sources).'" sizes="auto, (max-width: '.$src['width'].'px) 100vw, '.$src['width'].'px"';
		}

		return $html.' itemprop="image" style="aspect-ratio: '.$ratio.';" />';
	}
}

if ( ! function_exists('wp_paginate_links'))
{
	/**
	 * Paginasi seperti paginate_links() (end_size 1, mid_size 3) dengan markup Blocksy.
	 * Halaman 1 = $base_path, halaman N = $base_path/page/N.
	 */
	function wp_paginate_links($base_path, $current, $total)
	{
		$url = function ($n) use ($base_path) {
			return site_url($n === 1 ? $base_path : $base_path.'/page/'.$n);
		};

		$links = array();
		$dots = FALSE;
		for ($n = 1; $n <= $total; $n++)
		{
			if ($n === $current)
			{
				$links[] = '<span aria-current="page" class="page-numbers current">'.$n.'</span>';
				$dots = TRUE;
			}
			elseif ($n <= 1 OR ($n >= $current - 3 && $n <= $current + 3) OR $n > $total - 1)
			{
				$links[] = '<a class="page-numbers" href="'.$url($n).'">'.$n.'</a>';
				$dots = TRUE;
			}
			elseif ($dots)
			{
				$links[] = '<span class="page-numbers dots">&hellip;</span>';
				$dots = FALSE;
			}
		}

		$html = '';
		if ($current > 1)
		{
			$html .= '<a class="prev page-numbers" rel="prev" href="'.$url($current - 1).'"><svg width="9px" height="9px" viewBox="0 0 15 15" fill="currentColor"><path d="M10.9,15c-0.2,0-0.4-0.1-0.6-0.2L3.6,8c-0.3-0.3-0.3-0.8,0-1.1l6.6-6.6c0.3-0.3,0.8-0.3,1.1,0c0.3,0.3,0.3,0.8,0,1.1L5.2,7.4l6.2,6.2c0.3,0.3,0.3,0.8,0,1.1C11.3,14.9,11.1,15,10.9,15z"/></svg>Prev</a>';
		}
		$html .= '<div class="ct-hidden-sm">'.implode("\n", $links).'</div>';
		if ($current < $total)
		{
			$html .= '<a class="next page-numbers" rel="next" href="'.$url($current + 1).'">Next <svg width="9px" height="9px" viewBox="0 0 15 15" fill="currentColor"><path d="M4.1,15c0.2,0,0.4-0.1,0.6-0.2L11.4,8c0.3-0.3,0.3-0.8,0-1.1L4.8,0.2C4.5-0.1,4-0.1,3.7,0.2C3.4,0.5,3.4,1,3.7,1.3l6.1,6.1l-6.2,6.2c-0.3,0.3-0.3,0.8,0,1.1C3.7,14.9,3.9,15,4.1,15z"/></svg></a>';
		}

		return $html;
	}
}

if ( ! function_exists('wp_encode_uri_component'))
{
	/**
	 * Setara encodeURIComponent() JavaScript (dipakai Blocksy untuk link share):
	 * seperti rawurlencode(), tetapi ! * ' ( ) tidak di-encode.
	 */
	function wp_encode_uri_component($str)
	{
		return strtr(rawurlencode($str), array('%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')'));
	}
}

if ( ! function_exists('wp_texturize'))
{
	/**
	 * Port wptexturize() WordPress untuk teks biasa (judul): tanda kutip, apostrof, dash, elipsis,
	 * tanda kali, dan & menjadi entitas. Teks di dalam tag HTML tidak diubah.
	 */
	function wp_texturize($text)
	{
		// Seperti WordPress: '<' tanpa penutup dianggap tag sampai akhir teks (tidak diubah).
		$parts = preg_split('/(<[^>]*>?)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		foreach ($parts as $i => $part)
		{
			if ($part === '' OR $part[0] === '<')
			{
				continue;
			}

			$part = str_replace(
				array('---', ' -- ', '--', ' - ', 'xn&#8211;', '...', '``', '\'\'', ' (tm)'),
				array('&#8212;', ' &#8212; ', '&#8211;', ' &#8211; ', 'xn--', '&#8230;', '&#8220;', '&#8221;', ' &#8482;'),
				$part
			);

			$spaces = '[\r\n\t ]|\xC2\xA0|&nbsp;';
			// Kutip tunggal & ganda pembuka / penutup, apostrof.
			$part = preg_replace("/'(?=\\d\\d(?:\\Z|(?![%\\d]|[.,]\\d)))/", '&#8217;', $part);
			$part = preg_replace("/(?<=\\A|[([{\"\\-]|&lt;|$spaces)'/", '&#8216;', $part);
			$part = preg_replace("/(?<=\\d)'/", '&#8242;', $part);
			$part = preg_replace("/'/", '&#8217;', $part);
			$part = preg_replace("/(?<=\\A|[([{\\-]|&lt;|$spaces)\"(?!$spaces)/", '&#8220;', $part);
			$part = preg_replace("/(?<=\\d)\"/", '&#8243;', $part);
			$part = preg_replace('/"/', '&#8221;', $part);
			$part = preg_replace('/\b(\d(?(?<=0)[\d\.,]+|[\d\.,]*))x(\d[\d\.,]*)\b/', '$1&#215;$2', $part);
			// & yang bukan bagian dari entitas.
			$part = preg_replace('/&(?!#(?:\d+|x[a-f0-9]+);|[a-z1-4]{1,8};)/i', '&#038;', $part);

			$parts[$i] = $part;
		}

		return implode('', $parts);
	}
}

if ( ! function_exists('wp_nav_title'))
{
	/**
	 * Judul di navigasi Previous/Next: judul mentah di-escape tanpa mengubah tanda kutip (seperti Blocksy).
	 */
	function wp_nav_title($title)
	{
		return htmlspecialchars($title, ENT_NOQUOTES, 'UTF-8');
	}
}

if ( ! function_exists('wp_document_title'))
{
	/**
	 * Isi <title> seperti wp_get_document_title(): bagian-bagian (teks mentah) digabung dengan " - ",
	 * lalu wptexturize dan esc_html (entitas yang sudah ada tidak di-encode ulang).
	 */
	function wp_document_title(array $parts)
	{
		return htmlspecialchars(wp_texturize(implode(' - ', $parts)), ENT_QUOTES, 'UTF-8', FALSE);
	}
}

if ( ! function_exists('wp_attr_title'))
{
	/**
	 * Judul untuk atribut HTML (mis. aria-label): wptexturize lalu esc_attr tanpa encode ulang entitas.
	 */
	function wp_attr_title($title)
	{
		return htmlspecialchars(wp_texturize($title), ENT_QUOTES, 'UTF-8', FALSE);
	}
}

if ( ! function_exists('wp_local_url'))
{
	/**
	 * URL ke domain utama (https://stmi.ac.id/...) diarahkan ke situs ini (keputusan: link absolut diubah).
	 * Subdomain lain dibiarkan.
	 */
	function wp_local_url($url)
	{
		if (preg_match('#^(?:https?:)?//(?:www\.)?stmi\.ac\.id(?![\w.-])/?(.*)$#i', $url, $m))
		{
			return site_url($m[1]);
		}

		return $url;
	}
}
