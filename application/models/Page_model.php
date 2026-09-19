<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Halaman statis (Page WordPress) di tabel pages.
 *
 * view NULL   : dikelola dari admin; template default (views/pages/_page.php) atau full-width (views/pages/_page_full.php).
 * view terisi : beranda, yang tetap berupa file view (config/pages.php); content hanya untuk pencarian.
 */
class Page_model extends CI_Model {

	const ADMIN_PER_PAGE = 30;

	/** Varian layout otomatis untuk halaman baru / hasil edit (lihat layout_for()). */
	const HEAD_DEFAULT = 'sejarah-kampus';
	const HEAD_ELEMENTOR = 'peraturan';
	const FOOT_DEFAULT = 'akademik';
	const FOOT_PDF = 'daftar-isian-penggunaan-anggaran';
	const FOOT_VIDEO = 'maklumat-pelayanan';
	const FOOT_ELEMENTOR = 'daftar-informasi';

	public function find($id)
	{
		return $this->db->get_where('pages', array('id' => (int) $id))->row_array();
	}

	/**
	 * Halaman ber-template standar yang terbit, untuk URL /<slug>.
	 */
	public function find_published($slug)
	{
		return $this->db->get_where('pages', array('slug' => (string) $slug, 'status' => 'publish', 'view' => NULL))->row_array();
	}

	public function admin_list(array $filter, $page)
	{
		$apply = function () use ($filter) {
			$this->db->from('pages p')->where('p.view', NULL);
			if ($filter['status'] !== '')
			{
				$this->db->where('p.status', $filter['status']);
			}
			if ($filter['q'] !== '')
			{
				$this->db->group_start()->like('p.title', $filter['q'])->or_like('p.slug', $filter['q'])->group_end();
			}
		};

		$apply();
		$total = $this->db->count_all_results();

		$apply();
		$rows = $this->db->select('p.id, p.slug, p.title, p.status, p.modified_at, p.content, a.display_name AS author_name')
			->join('authors a', 'a.id = p.author_id', 'left')
			->order_by('p.title', 'ASC')
			->limit(self::ADMIN_PER_PAGE, ($page - 1) * self::ADMIN_PER_PAGE)
			->get()->result_array();

		foreach ($rows as &$r)
		{
			$r['elementor'] = self::is_elementor($r['content']);
			unset($r['content']);
		}

		return array($rows, $total);
	}

	public function counts()
	{
		$counts = array('all' => 0, 'publish' => 0, 'draft' => 0);
		foreach ($this->db->select('status, COUNT(*) AS n')->where('view', NULL)->group_by('status')->get('pages')->result_array() as $r)
		{
			$counts[$r['status']] = (int) $r['n'];
			$counts['all'] += (int) $r['n'];
		}

		return $counts;
	}

	public function slug_taken($slug, $except_id = NULL)
	{
		$this->db->where('slug', $slug);
		if ($except_id)
		{
			$this->db->where('id !=', (int) $except_id);
		}

		return $this->db->count_all_results('pages') > 0;
	}

	/**
	 * Simpan halaman. Slug yang berubah ikut diperbarui di item menu yang menautkannya.
	 */
	public function save($id, array $data)
	{
		$this->db->trans_start();
		if ($id)
		{
			$old = $this->find($id);
			$this->db->update('pages', $data, array('id' => (int) $id));
			if ($old && isset($data['slug']) && $old['slug'] !== $data['slug'])
			{
				$this->db->update('menu_items', array('slug' => $data['slug']), array('type' => 'page', 'slug' => $old['slug']));
			}
		}
		else
		{
			$this->db->insert('pages', $data);
			$id = $this->db->insert_id();
		}
		$this->db->trans_complete();

		return $this->db->trans_status() ? (int) $id : FALSE;
	}

	public function delete($id)
	{
		$this->db->delete('pages', array('id' => (int) $id));
	}

	/**
	 * Jumlah item menu yang menautkan halaman ini.
	 */
	public function menu_usage(array $page)
	{
		return $this->db->where(array('type' => 'page', 'slug' => ($page['slug'] === 'home') ? '' : $page['slug']))->count_all_results('menu_items');
	}

	/**
	 * Semua halaman (termasuk yang ber-view khusus) untuk pilihan menu: slug menu => [title, object_id, status].
	 * Beranda memakai slug menu ''.
	 */
	public function options()
	{
		$options = array();
		foreach ($this->db->select('id, slug, title, status')->get('pages')->result_array() as $p)
		{
			$options[($p['slug'] === 'home') ? '' : $p['slug']] = array(
				'title'     => $p['title'],
				'object_id' => (int) $p['id'],
				'status'    => $p['status'],
			);
		}

		return $options;
	}

	public static function is_elementor($content)
	{
		return strpos((string) $content, 'data-elementor-type=') !== FALSE;
	}

	/**
	 * Varian head/foot untuk konten ini. Varian yang sudah dipakai halaman dipertahankan selama aset yang
	 * dibutuhkan konten ada di dalamnya (hasil impor WordPress tetap identik); selain itu dipilih otomatis:
	 * PDF Embedder (class pdfemb-viewer) -> foot PDF, video -> foot mediaelement, Elementor -> aset Elementor.
	 * Mengembalikan [head, foot].
	 */
	public function layout_for($content, $head = NULL, $foot = NULL)
	{
		$elementor = self::is_elementor($content);
		$needs = array();
		if (strpos($content, 'pdfemb-viewer') !== FALSE)
		{
			$needs[] = 'pdfemb';
		}
		if (preg_match('/<video\b|wp-video-shortcode/', $content))
		{
			$needs[] = 'mediaelement';
		}
		if ($elementor)
		{
			$needs[] = 'elementor-frontend-js';
		}

		if ($head === NULL OR ($elementor && ! $this->variant_has('head', $head, 'elementor-frontend-css')))
		{
			$head = $elementor ? self::HEAD_ELEMENTOR : self::HEAD_DEFAULT;
		}

		$ok = ($foot !== NULL);
		foreach ($needs as $asset)
		{
			$ok = $ok && $this->variant_has('foot', $foot, $asset);
		}
		if ( ! $ok)
		{
			$foot = in_array('pdfemb', $needs, TRUE) ? self::FOOT_PDF
				: (in_array('mediaelement', $needs, TRUE) ? self::FOOT_VIDEO : ($elementor ? self::FOOT_ELEMENTOR : self::FOOT_DEFAULT));
		}

		return array($head, $foot);
	}

	/**
	 * Apakah varian layout memuat aset tertentu (dicari dari id/nama file di dalamnya).
	 */
	public function variant_has($type, $name, $needle)
	{
		static $cache = array();
		$file = APPPATH.'views/layouts/'.$type.'/'.$name.'.php';
		if ( ! isset($cache[$file]))
		{
			$cache[$file] = is_file($file) ? file_get_contents($file) : '';
		}

		return strpos($cache[$file], $needle) !== FALSE;
	}

	/**
	 * fetchpriority/loading gambar di luar konten, mengikuti wp_get_loading_optimization_attributes():
	 * gambar konten yang punya width & height dihitung lebih dulu, 3 gambar pertama tidak lazy, gambar besar
	 * pertama mendapat fetchpriority="high". Urutan setelah konten: logo sticky, logo default, avatar author.
	 * Mengembalikan [img_hints layout, atribut avatar].
	 */
	public function img_hints($content)
	{
		$count = 0;
		if (preg_match_all('/<img\b[^>]*>/', $content, $m))
		{
			foreach ($m[0] as $img)
			{
				if (strpos($img, 'width=') !== FALSE && strpos($img, 'height=') !== FALSE)
				{
					$count++;
				}
			}
		}
		$priority = (strpos($content, 'fetchpriority="high"') !== FALSE);

		$next = function ($large) use (&$count, &$priority) {
			$count++;
			if ($count > 3)
			{
				return 'loading="lazy" ';
			}
			if ( ! $priority && $large)
			{
				$priority = TRUE;
				return 'fetchpriority="high" ';
			}
			return '';
		};

		// Logo sticky & default dirender sekali lalu dipakai ulang di header mobile.
		$sticky = $next(TRUE);
		$default = $next(TRUE);
		$avatar = $next(FALSE);
		$hints = array('header:0' => $sticky, 'header:1' => $default, 'header:2' => $sticky, 'header:3' => $default,
			'footer:0' => 'loading="lazy" ');

		return array(array_filter($hints), $avatar);
	}
}
