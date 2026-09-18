<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_model extends CI_Model {

	public function get_all()
	{
		$query = $this->db->get('contacts');
		$result = array();
		foreach ($query->result_array() as $row)
		{
			$result[$row['key']] = $row['value'];
		}
		return $result;
	}

	public function update_all($data)
	{
		$this->db->trans_start();
		foreach ($data as $key => $value)
		{
			$this->db->where('key', $key);
			$this->db->update('contacts', array('value' => $value));
		}
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
}
