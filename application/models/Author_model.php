<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Author post sekaligus pengguna panel admin.
 */
class Author_model extends CI_Model {

	public function find($id)
	{
		return $this->db->where('id', (int) $id)->get('authors')->row_array();
	}

	public function find_by_username($username)
	{
		return $this->db->where('username', $username)->get('authors')->row_array();
	}

	public function all()
	{
		return $this->db
			->select('a.*, (SELECT COUNT(*) FROM posts p WHERE p.author_id = a.id) AS post_count', FALSE)
			->from('authors a')
			->order_by('a.display_name')
			->get()
			->result_array();
	}

	public function options()
	{
		$out = array();
		foreach ($this->db->select('id, display_name')->order_by('display_name')->get('authors')->result_array() as $a)
		{
			$out[$a['id']] = $a['display_name'];
		}

		return $out;
	}

	public function create(array $data)
	{
		$this->db->insert('authors', $data);

		return $this->db->insert_id();
	}

	public function update($id, array $data)
	{
		return $this->db->where('id', (int) $id)->update('authors', $data);
	}

	public function delete($id)
	{
		return $this->db->where('id', (int) $id)->delete('authors');
	}

	public function slug_taken($slug, $except_id = NULL)
	{
		$this->db->where('slug', $slug);
		if ($except_id)
		{
			$this->db->where('id !=', (int) $except_id);
		}

		return $this->db->count_all_results('authors') > 0;
	}

	public function username_taken($username, $except_id = NULL)
	{
		$this->db->where('username', $username);
		if ($except_id)
		{
			$this->db->where('id !=', (int) $except_id);
		}

		return $this->db->count_all_results('authors') > 0;
	}

	public function touch_login($id)
	{
		$this->db->where('id', (int) $id)->update('authors', array('last_login_at' => date('Y-m-d H:i:s')));
	}

	/**
	 * Hash Gravatar seperti WordPress 6.8: sha256 dari email huruf kecil.
	 */
	public static function gravatar_hash($email)
	{
		return hash('sha256', strtolower(trim($email)));
	}
}
