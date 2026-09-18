<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_featured_link_model extends CI_Model {

	public function all()
	{
		return $this->db->order_by('row_number', 'ASC')
						->order_by('order_num', 'ASC')
						->order_by('id', 'ASC')
						->get('home_featured_links')
						->result_array();
	}

	/**
	 * Returns links grouped by row_number.
	 * Example: [1 => [...items...], 2 => [...items...]]
	 */
	public function grouped_by_row()
	{
		$links = $this->all();
		$grouped = array();
		foreach ($links as $link) {
			$row = (int) $link['row_number'];
			$grouped[$row][] = $link;
		}
		ksort($grouped);
		return $grouped;
	}

	public function find($id)
	{
		return $this->db->where('id', $id)->get('home_featured_links')->row_array();
	}

	public function update($id, $data)
	{
		return $this->db->where('id', $id)->update('home_featured_links', $data);
	}

	public function insert($data)
	{
		$this->db->insert('home_featured_links', $data);
		return $this->db->insert_id();
	}

	public function delete($id)
	{
		return $this->db->where('id', $id)->delete('home_featured_links');
	}
}
