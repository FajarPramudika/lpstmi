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
