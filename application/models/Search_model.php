<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pencarian seperti WordPress (WP_Query::parse_search / parse_search_order) atas post, paket download,
 * dan halaman statis (tabel pages).
 *
 * - Kata kunci dipecah per kata ("frasa dalam kutip" tetap satu), kata tunggal a-z dan stopword Inggris dibuang,
 *   kata berawalan "-" berarti "tidak mengandung".
 * - Setiap kata harus ada di judul ATAU konten (LIKE, tidak peka huruf besar/kecil).
 * - Urutan: 1 kata -> judul mengandung kata dulu, lalu tanggal terbaru. Banyak kata -> judul memuat seluruh frasa,
 *   judul memuat semua kata, judul memuat salah satu kata, konten memuat frasa, lainnya; lalu tanggal terbaru.
 */
class Search_model extends CI_Model {

	const PER_PAGE = 5;

	/** Halaman terjauh yang dilayani; di luar itu 404 tanpa menjalankan kueri. */
	const MAX_PAGE = 200;

	/** Panjang maksimum kata kunci yang diproses (pola LIKE panjang mahal dan tidak berguna). */
	const MAX_QUERY = 128;

	/** Request pencarian per IP per menit (halaman hasil maupun endpoint live search). */
	const RATE_LIMIT = 60;

	/** Stopword bawaan WordPress (wp_get_search_stopwords). */
	protected $stopwords = array('about', 'an', 'are', 'as', 'at', 'be', 'by', 'com', 'for', 'from', 'how', 'in', 'is', 'it',
		'of', 'on', 'or', 'that', 'the', 'this', 'to', 'was', 'what', 'when', 'where', 'who', 'will', 'with', 'www');

	/**
	 * Hasil pencarian: [baris, total]. $types: subset dari post, download, page.
	 * Baris: kind, id, slug, title, body, published_at, author_id, featured_media_id.
	 */
	public function search($query, $page, $per_page = self::PER_PAGE, array $types = array('post', 'download', 'page'))
	{
		$sources = array(
			'post'     => "SELECT 'post' AS kind, id, slug, title, content AS body, published_at, author_id, featured_media_id FROM posts WHERE status = 'publish'",
			'download' => "SELECT 'download' AS kind, id, slug, title, description AS body, published_at, author_id, featured_media_id FROM downloads WHERE status = 'publish'",
			'page'     => "SELECT 'page' AS kind, id, slug, title, content AS body, published_at, author_id, featured_media_id FROM pages WHERE status = 'publish'",
		);
		$union = implode(' UNION ALL ', array_intersect_key($sources, array_flip($types)));

		list($where, $order) = $this->clauses(mb_substr((string) $query, 0, self::MAX_QUERY));

		$total = (int) $this->db->query("SELECT COUNT(*) AS n FROM ($union) s".($where ? " WHERE $where" : ''))->row()->n;
		$rows = $this->db->query("SELECT s.* FROM ($union) s".($where ? " WHERE $where" : '')
			." ORDER BY ".($order ? "$order, " : '')."s.published_at DESC LIMIT ".(int) (($page - 1) * $per_page).', '.(int) $per_page)
			->result_array();

		return array($rows, $total);
	}

	/**
	 * Pecah kata kunci seperti WP_Query::parse_search() + parse_search_terms().
	 */
	public function terms($query)
	{
		$terms = array();
		if (preg_match_all('/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $query, $m))
		{
			foreach ($m[0] as $term)
			{
				$term = preg_match('/^".+"$/', $term) ? trim($term, "\"'") : trim($term, "\"' ");
				if ($term === '' OR (strlen($term) === 1 && preg_match('/^[a-z\-]$/i', $term)))
				{
					continue;
				}
				if (in_array(mb_strtolower($term), $this->stopwords, TRUE))
				{
					continue;
				}
				$terms[] = $term;
			}
		}
		if (empty($terms) OR count($terms) > 9)
		{
			$terms = array($query);
		}

		return $terms;
	}

	/**
	 * [WHERE, ORDER BY] untuk kata kunci (string kosong = semua konten, urut tanggal).
	 */
	protected function clauses($query)
	{
		if ($query === '')
		{
			return array('', '');
		}

		$db = $this->db;
		$terms = $this->terms($query);
		$where = array();
		$title_likes = array();

		foreach ($terms as $term)
		{
			$exclude = (strlen($term) > 1 && $term[0] === '-');
			if ($exclude)
			{
				$term = substr($term, 1);
			}
			$like = $db->escape('%'.$db->escape_like_str($term).'%')." ESCAPE '!'";
			if ($exclude)
			{
				$where[] = "(s.title NOT LIKE $like AND s.body NOT LIKE $like)";
			}
			else
			{
				$where[] = "(s.title LIKE $like OR s.body LIKE $like)";
				$title_likes[] = "s.title LIKE $like";
			}
		}

		if (count($terms) > 1)
		{
			$order = '';
			$sentence = preg_match('/(?:\s|^)\-/', $query) ? NULL : $db->escape('%'.$db->escape_like_str($query).'%')." ESCAPE '!'";
			if ($sentence)
			{
				$order .= "WHEN s.title LIKE $sentence THEN 1 ";
			}
			if ($title_likes && count($title_likes) < 7)
			{
				$order .= 'WHEN '.implode(' AND ', $title_likes).' THEN 2 ';
				if (count($title_likes) > 1)
				{
					$order .= 'WHEN '.implode(' OR ', $title_likes).' THEN 3 ';
				}
			}
			if ($sentence)
			{
				$order .= "WHEN s.body LIKE $sentence THEN 5 ";
			}
			$order = $order ? "(CASE $order ELSE 6 END)" : '';
		}
		else
		{
			$order = $title_likes ? reset($title_likes).' DESC' : '';
		}

		return array(implode(' AND ', $where), $order);
	}
}
