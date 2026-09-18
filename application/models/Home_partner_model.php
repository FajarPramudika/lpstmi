<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_partner_model extends CI_Model {

	public function all()
	{
		$this->db->order_by('order_num', 'ASC');
		$this->db->order_by('id', 'ASC');
		return $this->db->get('home_partners')->result_array();
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
