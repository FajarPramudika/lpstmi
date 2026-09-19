<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_partner_model extends CI_Model {

	/**
	 * Semua logo mitra, dengan media_id dari pustaka media (untuk kelas wp-image-<ID> seperti WordPress).
	 */
	public function all()
	{
		return $this->db->select('p.*, m.id AS media_id')
			->from('home_partners p')
			->join('media m', "CONCAT('wp-content/uploads/', m.file) = p.image_path", 'left')
			->order_by('p.order_num', 'ASC')
			->order_by('p.id', 'ASC')
			->get()->result_array();
	}

	public function find($id)
	{
		return $this->db->get_where('home_partners', array('id' => $id))->row_array();
	}

	public function insert($data)
	{
		$this->db->insert('home_partners', $data);
		return $this->db->insert_id();
	}

	public function update($id, $data)
	{
		return $this->db->where('id', $id)->update('home_partners', $data);
	}

	public function delete($id)
	{
		return $this->db->where('id', $id)->delete('home_partners');
	}
}
