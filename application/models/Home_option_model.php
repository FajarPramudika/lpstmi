<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_option_model extends CI_Model {

	public function get($key, $default = NULL)
	{
		$row = $this->db->get_where('home_options', array('key' => $key))->row_array();
		return $row ? $row['value'] : $default;
	}

	public function get_all()
	{
		$rows = $this->db->get('home_options')->result_array();
		$opts = array();
		foreach ($rows as $row) {
			$opts[$row['key']] = $row['value'];
		}
		return $opts;
	}

	public function set($key, $value)
	{
		$exists = $this->db->get_where('home_options', array('key' => $key))->num_rows() > 0;
		if ($exists) {
			$this->db->update('home_options', array('value' => $value), array('key' => $key));
		} else {
			$this->db->insert('home_options', array('key' => $key, 'value' => $value));
		}
	}
}
