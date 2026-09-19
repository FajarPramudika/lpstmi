<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Paket Download Manager (tabel downloads).
 */
class Download_model extends CI_Model {

	const ADMIN_PER_PAGE = 20;

	public function find($id)
	{
		return $this->db->where('id', (int) $id)->get('downloads')->row_array();
	}

	public function find_published($slug)
	{
		return $this->db->where('slug', $slug)->where('status', 'publish')->get('downloads')->row_array();
	}

	public function find_published_id($id)
	{
		return $this->db->where('id', (int) $id)->where('status', 'publish')->get('downloads')->row_array();
	}

	/**
	 * Tambah hitungan download (atomik).
	 */
	public function increment($id)
	{
		$this->db->set('download_count', 'download_count + 1', FALSE)->where('id', (int) $id)->update('downloads');
	}

	/**
	 * Path absolut file lokal, atau NULL jika file di situs luar / tidak ada / di luar folder uploads.
	 */
	public static function local_path(array $download)
	{
		if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $download['file']))
		{
			return NULL;
		}
		$base = realpath(FCPATH.'wp-content/uploads');
		$path = realpath($base.'/'.$download['file']);

		return ($path && strpos($path, $base.DIRECTORY_SEPARATOR) === 0 && is_file($path)) ? $path : NULL;
	}

	public static function is_external(array $download)
	{
		return (bool) preg_match('#^https?://#i', $download['file']);
	}

	/* ------------------------------------------------------------------
	 * Panel admin
	 * ------------------------------------------------------------------ */

	public function admin_list(array $filter, $page)
	{
		$build = function () use ($filter) {
			$this->db->from('downloads');
			if ( ! empty($filter['status']))
			{
				$this->db->where('status', $filter['status']);
			}
			if ( ! empty($filter['q']))
			{
				$this->db->like('title', $filter['q']);
			}
		};

		$build();
		$total = $this->db->count_all_results();
		$build();
		$rows = $this->db
			->select('id, slug, title, status, file, file_size, download_count, published_at, modified_at')
			->order_by('published_at', 'DESC')
			->limit(self::ADMIN_PER_PAGE, ($page - 1) * self::ADMIN_PER_PAGE)
			->get()
			->result_array();

		return array($rows, $total);
	}

	public function slug_taken($slug, $except_id = NULL)
	{
		$this->db->where('slug', $slug);
		if ($except_id)
		{
			$this->db->where('id !=', (int) $except_id);
		}

		return $this->db->count_all_results('downloads') > 0;
	}

	public function save($id, array $data)
	{
		if ($id)
		{
			$this->db->where('id', (int) $id)->update('downloads', $data);

			return (int) $id;
		}
		$this->db->insert('downloads', $data);

		return (int) $this->db->insert_id();
	}

	public function delete($id)
	{
		return $this->db->where('id', (int) $id)->delete('downloads');
	}

	public function counts()
	{
		$out = array('publish' => 0, 'draft' => 0);
		foreach ($this->db->select('status, COUNT(*) AS n')->group_by('status')->get('downloads')->result_array() as $r)
		{
			$out[$r['status']] = (int) $r['n'];
		}

		return $out;
	}

	/**
	 * Label ukuran file seperti Download Manager: "734.35 KB", "7.32 MB".
	 */
	public static function size_label($bytes)
	{
		$kb = $bytes / 1024;

		return ($kb < 1024) ? number_format($kb, 2, '.', '').' KB' : number_format($kb / 1024, 2, '.', '').' MB';
	}

	/** Kode pendek kartu download di konten post/halaman, sama dengan shortcode Download Manager. */
	const SHORTCODE = '/\[wpdm_package\s+id=["\']?(\d+)["\']?\s*\]/';

	/**
	 * Ikon kartu: kolom icon, atau otomatis dari ekstensi file (file-type-icons/<ext>.svg); URL luar = web.
	 */
	public static function icon(array $download)
	{
		if ( ! empty($download['icon']))
		{
			return $download['icon'];
		}
		if (self::is_external($download))
		{
			return 'web';
		}
		$ext = strtolower(pathinfo($download['file'], PATHINFO_EXTENSION));

		return is_file(FCPATH.'wp-content/plugins/download-manager/assets/file-type-icons/'.$ext.'.svg') ? $ext : 'web';
	}

	/**
	 * Kartu "WPDM Link Template: Default Template" untuk satu paket (views/downloads/_card.php).
	 * refresh = parameter anti-cache acak seperti WPDM (uniqid + time).
	 * Judul: di konten biasa WordPress menjalankan wptexturize sebelum shortcode, jadi judul kartu tampil mentah;
	 * di konten Elementor shortcode dirender di dalam widget lebih dulu, jadi judulnya ikut di-texturize.
	 */
	public function card(array $download, $refresh = NULL, $texturize = FALSE)
	{
		return $this->load->view('downloads/_card', array(
			'download' => $download,
			'title'    => $texturize ? wp_texturize($download['title']) : wp_nav_title($download['title']),
			'icon'     => self::icon($download),
			'refresh'  => ($refresh !== NULL) ? $refresh : uniqid().time(),
		), TRUE);
	}

	/**
	 * Ganti [wpdm_package id='N'] dengan kartu paketnya. Paket yang tidak ada / draft tidak menampilkan apa pun.
	 */
	public function render_shortcodes($html)
	{
		$texturize = (strpos($html, 'data-elementor-type=') !== FALSE);
		if (strpos($html, '[wpdm_package') === FALSE OR ! preg_match_all(self::SHORTCODE, $html, $m))
		{
			return $html;
		}

		$packages = array();
		foreach ($this->db->where_in('id', array_map('intval', $m[1]))->where('status', 'publish')->get('downloads')->result_array() as $d)
		{
			$packages[$d['id']] = $d;
		}

		return preg_replace_callback(self::SHORTCODE, function ($x) use ($packages, $texturize) {
			return isset($packages[$x[1]]) ? $this->card($packages[$x[1]], NULL, $texturize) : '';
		}, $html);
	}
}
