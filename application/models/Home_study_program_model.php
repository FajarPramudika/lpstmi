<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_study_program_model extends CI_Model {

	public function all()
	{
		return $this->db->order_by('order_num', 'ASC')
						->order_by('id', 'ASC')
						->get('home_study_programs')
						->result_array();
	}

	public function find($id)
	{
		return $this->db->where('id', $id)->get('home_study_programs')->row_array();
	}

	public function update($id, $data)
	{
		return $this->db->where('id', $id)->update('home_study_programs', $data);
	}

	public function insert($data)
	{
		$this->db->insert('home_study_programs', $data);
		return $this->db->insert_id();
	}

	public function delete($id)
	{
		return $this->db->where('id', $id)->delete('home_study_programs');
	}
}
