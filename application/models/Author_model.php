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

	/**
	 * Cari akun untuk login: boleh dengan username ATAU email.
	 *
	 * Hanya akun yang punya username yang bisa login. Itu mempertahankan arti "username dikosongkan =
	 * tanpa akses admin": tanpa syarat ini, mengosongkan username tidak lagi mencabut akses karena
	 * akunnya masih bisa masuk lewat email.
	 *
	 * Perbandingan tidak peka huruf besar/kecil (collation utf8mb4_general_ci), sesuai kebiasaan email.
	 * Kalau ada lebih dari satu kecocokan (data lama sebelum email dibuat unik), akun ditolak daripada
	 * ditebak — gagal ke arah yang aman.
	 */
	public function find_by_login($identifier)
	{
		$identifier = trim((string) $identifier);
		if ($identifier === '')
		{
			return NULL;
		}

		$rows = $this->db
			->where('username IS NOT NULL', NULL, FALSE)
			->group_start()->where('username', $identifier)->or_where('email', $identifier)->group_end()
			->limit(2)
			->get('authors')
			->result_array();

		return (count($rows) === 1) ? $rows[0] : NULL;
	}

	/**
	 * Apakah email sudah dipakai akun lain. Email ikut menjadi identitas login, jadi harus unik.
	 */
	public function email_taken($email, $except_id = NULL)
	{
		$this->db->where('email', $email);
		if ($except_id)
		{
			$this->db->where('id !=', (int) $except_id);
		}

		return $this->db->count_all_results('authors') > 0;
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
