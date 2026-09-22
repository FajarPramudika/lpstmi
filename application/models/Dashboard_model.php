<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Data ringkas untuk dasbor admin: jumlah per jenis konten dan konten yang terakhir diubah
 * (post, halaman, paket download dalam satu daftar).
 */
class Dashboard_model extends CI_Model {

	/**
	 * array('post' => array('publish' => n, 'draft' => n), 'page' => ..., 'download' => ...)
	 * Halaman hanya yang dikelola lewat admin Halaman (view NULL), sama seperti Page_model::counts().
	 */
	public function status_counts()
	{
		$out = array();
		foreach (array('post' => 'posts', 'page' => 'pages', 'download' => 'downloads') as $type => $table)
		{
			$out[$type] = array('publish' => 0, 'draft' => 0);
			$this->db->select('status, COUNT(*) AS n')->group_by('status');
			if ($table === 'pages')
			{
				$this->db->where('view', NULL);
			}
			foreach ($this->db->get($table)->result_array() as $r)
			{
				$out[$type][$r['status']] = (int) $r['n'];
			}
		}

		return $out;
	}

	/** Total hitungan unduhan semua paket. */
	public function download_total()
	{
		return (int) $this->db->select_sum('download_count', 'n')->get('downloads')->row()->n;
	}

	/**
	 * Konten yang terakhir diubah, terbaru dulu: type, id, slug, title (mentah), status, modified_at, author_name.
	 */
	public function recent_changes($limit = 10)
	{
		$sql = '(SELECT \'post\' AS type, id, slug, title, status, modified_at, author_id FROM posts)'
			.' UNION ALL (SELECT \'page\', id, slug, title, status, modified_at, author_id FROM pages WHERE view IS NULL)'
			.' UNION ALL (SELECT \'download\', id, slug, title, status, modified_at, author_id FROM downloads)';

		return $this->db->query('SELECT c.*, a.display_name AS author_name FROM ('.$sql.') c'
			.' LEFT JOIN authors a ON a.id = c.author_id'
			.' ORDER BY c.modified_at DESC, c.id DESC LIMIT ?', array((int) $limit))->result_array();
	}

	/** URL publik konten (hanya bermakna untuk yang terbit). */
	public static function public_url(array $row)
	{
		return site_url(($row['type'] === 'download' ? 'download/' : '').$row['slug']);
	}
}
