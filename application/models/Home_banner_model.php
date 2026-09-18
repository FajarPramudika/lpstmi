<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_banner_model extends CI_Model {

	public function all()
	{
		$this->db->order_by('order_num', 'ASC');
		$this->db->order_by('id', 'ASC');
		return $this->db->get('home_banners')->result_array();
	}

	public function find($id)
	{
		return $this->db->get_where('home_banners', array('id' => $id))->row_array();
	}

	public function insert($data)
	{
		$this->db->insert('home_banners', $data);
		return $this->db->insert_id();
	}

	public function update($id, $data)
	{
		return $this->db->where('id', $id)->update('home_banners', $data);
	}

	public function delete($id)
	{
		return $this->db->where('id', $id)->delete('home_banners');
	}
}
