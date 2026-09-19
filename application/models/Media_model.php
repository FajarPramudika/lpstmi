<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Media (file di wp-content/uploads/).
 */
class Media_model extends CI_Model {

	const PER_PAGE = 40;

	/**
	 * [daftar, total]. $type: 'image' | 'file' | NULL (semua).
	 */
	public function paginate($page, $type = NULL, $search = '')
	{
		$filter = function () use ($type, $search) {
			if ($type === 'image')
			{
				$this->db->like('mime_type', 'image/', 'after');
			}
			elseif ($type === 'file')
			{
				$this->db->not_like('mime_type', 'image/', 'after');
			}
			if ($search !== '')
			{
				$this->db->like('file', $search);
			}
		};

		$filter();
		$total = $this->db->count_all_results('media');
		$filter();
		$rows = $this->db->order_by('created_at', 'DESC')->order_by('id', 'DESC')
			->limit(self::PER_PAGE, ($page - 1) * self::PER_PAGE)->get('media')->result_array();

		return array($rows, $total);
	}

	public function find($id)
	{
		return $this->db->where('id', (int) $id)->get('media')->row_array();
	}

	public function create(array $row)
	{
		$this->db->insert('media', $row);

		return $this->db->insert_id();
	}

	public function update($id, array $data)
	{
		return $this->db->where('id', (int) $id)->update('media', $data);
	}

	/**
	 * Jumlah post yang memakai media ini sebagai gambar unggulan.
	 */
	public function usage($id)
	{
		return $this->db->where('featured_media_id', (int) $id)->count_all_results('posts');
	}

	/**
	 * Tempat file media ini (asli atau ukuran mana pun) masih dipakai di konten: post, halaman, paket download,
	 * beranda, atau view situs. Mengembalikan daftar keterangan; kosong = aman dihapus.
	 */
	public function content_usage(array $media)
	{
		$base = FCPATH.'wp-content/uploads/';
		$needles = array();
		foreach ($this->files($media) as $f)
		{
			$needles[] = substr($f, strlen($base));
		}

		$found = array();
		$tables = array(
			'posts'               => array(array('content', 'excerpt'), 'title', 'post'),
			'pages'               => array(array('content'), 'title', 'halaman'),
			'downloads'           => array(array('description', 'file'), 'title', 'paket download'),
			'home_banners'        => array(array('image_path', 'image_srcset'), NULL, 'banner beranda'),
			'home_partners'       => array(array('image_path', 'image_srcset'), 'name', 'mitra beranda'),
			'home_featured_links' => array(array('image_path', 'image_srcset'), NULL, 'link unggulan beranda'),
			'home_study_programs' => array(array('description', 'icon_svg'), 'title', 'program studi beranda'),
			'home_options'        => array(array('value'), 'key', 'pengaturan beranda'),
		);
		foreach ($tables as $table => $t)
		{
			$this->db->group_start();
			foreach ($t[0] as $col)
			{
				foreach ($needles as $n)
				{
					$this->db->or_like($col, $n);
				}
			}
			$this->db->group_end();
			foreach ($this->db->select($t[1] !== NULL ? '`'.$t[1].'` AS label' : '1 AS label', FALSE)->get($table)->result_array() as $r)
			{
				$found[] = $t[2].($t[1] !== NULL ? ' "'.$r['label'].'"' : '');
			}
		}

		// View situs (layout, beranda, 404): markup hasil migrasi yang menyebut file ini.
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(APPPATH.'views', FilesystemIterator::SKIP_DOTS)) as $f)
		{
			$rel = substr($f->getPathname(), strlen(APPPATH.'views/'));
			if (strpos($rel, 'admin/') === 0)
			{
				continue;
			}
			$src = file_get_contents($f->getPathname());
			foreach ($needles as $n)
			{
				if (strpos($src, $n) !== FALSE)
				{
					$found[] = 'view '.$rel;
					break;
				}
			}
		}

		return array_values(array_unique($found));
	}

	/**
	 * Hapus baris dan semua file (asli + ukuran turunan).
	 */
	public function delete($id)
	{
		$media = $this->find($id);
		if ( ! $media)
		{
			return FALSE;
		}

		foreach ($this->files($media) as $file)
		{
			if (is_file($file))
			{
				@unlink($file);
			}
		}

		return $this->db->where('id', (int) $id)->delete('media');
	}

	public function files(array $media)
	{
		$base = FCPATH.'wp-content/uploads/';
		$dir = dirname($media['file']).'/';
		$files = array($base.$media['file']);
		if (preg_match('/^(.*)-scaled(\.[a-z0-9]+)$/i', $media['file'], $m))
		{
			$files[] = $base.$m[1].$m[2];
		}
		foreach ((array) json_decode($media['sizes'], TRUE) as $size)
		{
			$files[] = $base.$dir.$size['file'];
		}

		return array_unique($files);
	}

	/**
	 * URL file asli dan thumbnail (untuk tampilan admin).
	 */
	public static function urls(array $media)
	{
		$sizes = (array) json_decode($media['sizes'], TRUE);
		$dir = dirname($media['file']).'/';
		$thumb = isset($sizes['thumbnail']) ? $dir.$sizes['thumbnail']['file']
			: (isset($sizes['medium']) ? $dir.$sizes['medium']['file'] : $media['file']);

		return array(
			'url'   => base_url('wp-content/uploads/'.$media['file']),
			'thumb' => base_url('wp-content/uploads/'.$thumb),
		);
	}
}
