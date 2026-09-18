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
