<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Footer_link_model extends CI_Model {

	public function all()
	{
		return $this->db->order_by('order_num', 'ASC')
		                ->order_by('id', 'ASC')
		                ->get('footer_links')
		                ->result_array();
	}

	public function find($id)
	{
		return $this->db->where('id', $id)
		                ->get('footer_links')
		                ->row_array();
	}

	public function create(array $data)
	{
		$this->db->insert('footer_links', $data);
		return $this->db->insert_id();
	}

	public function update($id, array $data)
	{
		$this->db->where('id', $id)->update('footer_links', $data);
	}

	public function delete($id)
	{
		$this->db->where('id', $id)->delete('footer_links');
	}
}
