<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('slugify'))
{
	/**
	 * Slug seperti sanitize_title() WordPress: huruf kecil, aksen dihapus, selain a-z0-9 menjadi '-'.
	 */
	function slugify($text)
	{
		$text = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');
		if (function_exists('iconv'))
		{
			$converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
			if ($converted !== FALSE)
			{
				$text = $converted;
			}
		}
		$text = strtolower($text);
		$text = preg_replace('/[^a-z0-9]+/', '-', $text);

		return substr(trim($text, '-'), 0, 190);
	}
}

if ( ! function_exists('unique_slug'))
{
	/**
	 * Tambah akhiran -2, -3, ... jika slug sudah dipakai ($taken($slug) mengembalikan TRUE).
	 */
	function unique_slug($slug, callable $taken)
	{
		if ($slug === '')
		{
			$slug = 'item';
		}
		$candidate = $slug;
		for ($i = 2; $taken($candidate); $i++)
		{
			$candidate = $slug.'-'.$i;
		}

		return $candidate;
	}
}

if ( ! function_exists('root_slug_conflict'))
{
	/**
	 * Post dan halaman sama-sama memakai URL satu segmen (/<slug>). Mengembalikan TRUE jika $slug sudah dipakai
	 * URL sistem, halaman berkerangka khusus (config/pages.php), atau jenis konten lain ($other: 'posts' | 'pages').
	 * Bentrok dengan konten sejenis diselesaikan unique_slug() (akhiran -2, -3, ...).
	 */
	function root_slug_conflict($slug, $other)
	{
		$reserved = array('admin', 'assets', 'author', 'category', 'download', 'error-404', 'feed', 'home', 'page', 'search',
			'tag', 'tools', 'wp-admin', 'wp-content', 'wp-includes', 'wp-json');
		if (in_array($slug, $reserved, TRUE))
		{
			return TRUE;
		}

		$CI =& get_instance();
		$CI->config->load('pages', TRUE);
		if (isset($CI->config->item('pages', 'pages')[$slug]))
		{
			return TRUE;
		}

		return $CI->db->where('slug', $slug)->count_all_results($other) > 0;
	}
}

if ( ! function_exists('content_to_tokens'))
{
	/**
	 * Simpan URL situs di konten sebagai token (kebalikan dari wp_content()), supaya
	 * data tetap benar walau base_url berubah (localhost -> produksi).
	 */
	function content_to_tokens($html)
	{
		return str_replace(
			array(base_url_json(), rawurlencode(base_url()), base_url()),
			array('{base_url_json}', '{base_url_encoded}', '{base_url}'),
			$html
		);
	}
}

if ( ! function_exists('auto_excerpt'))
{
	/**
	 * Excerpt otomatis seperti kartu arsip Blocksy: 40 kata pertama teks konten + "…".
	 */
	function auto_excerpt($content)
	{
		$text = preg_replace('/\[[^\]]*\]/', '', $content);
		$text = preg_replace('#<(script|style)\b.*?</\1>#is', '', $text);
		// Antar-paragraf/baris dipisah spasi agar kata tidak menempel setelah tag dibuang.
		$text = preg_replace('#<br\s*/?>|</(?:p|div|li|h[1-6]|td|th|blockquote)>#i', '$0 ', $text);
		$text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8')));
		$words = preg_split('/ /u', $text, -1, PREG_SPLIT_NO_EMPTY);

		if (count($words) > 40)
		{
			$text = implode(' ', array_slice($words, 0, 40)).'…';
		}

		return '<p>'.htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8', FALSE)."</p>\n";
	}
}

if ( ! function_exists('admin_pagination'))
{
	/**
	 * Link halaman sederhana untuk daftar di admin. $url($page) menghasilkan URL.
	 */
	function admin_pagination($page, $total_pages, callable $url)
	{
		if ($total_pages <= 1)
		{
			return '';
		}

		$html = '<nav class="pager">';
		if ($page > 1)
		{
			$html .= '<a href="'.html_escape($url($page - 1)).'">&larr; Sebelumnya</a>';
		}
		$html .= '<span>Halaman '.$page.' dari '.$total_pages.'</span>';
		if ($page < $total_pages)
		{
			$html .= '<a href="'.html_escape($url($page + 1)).'">Berikutnya &rarr;</a>';
		}

		return $html.'</nav>';
	}
}

if ( ! function_exists('admin_url_query'))
{
	/**
	 * URL admin dengan query string (nilai kosong dibuang).
	 */
	function admin_url_query($path, array $query)
	{
		$query = array_filter($query, function ($v) {
			return $v !== NULL && $v !== '' && $v !== 0;
		});

		return site_url($path).(empty($query) ? '' : '?'.http_build_query($query));
	}
}

if ( ! function_exists('admin_icon'))
{
	/**
	 * Ikon garis 18px (gaya Feather) untuk panel admin: sidebar dan dasbor. Hanya path SVG, dekoratif.
	 */
	function admin_icon($name)
	{
		static $icons = array(
			'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
			'post'      => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/>',
			'page'      => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M4 8h16M8 12h8M8 16h5"/>',
			'download'  => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/>',
			'media'     => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>',
			'category'  => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
			'tag'       => '<path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.2"/>',
			'home'      => '<path d="M3 10.5L12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1z"/>',
			'menu'      => '<path d="M4 6h16M4 12h16M4 18h10"/>',
			'link'      => '<path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"/>',
			'contact'   => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
			'users'     => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
			'profile'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/>',
			'external'  => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/>',
			'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
		);

		return '<svg class="nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$icons[$name].'</svg>';
	}
}
