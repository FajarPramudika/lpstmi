<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_row_number_to_featured_links extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_column('home_featured_links', array(
			'row_number' => array('type' => 'INT', 'default' => 1, 'after' => 'order_num'),
		));

		// Set default row_number based on original layout: first 5 = row 1, rest = row 2
		$this->db->where('order_num <=', 5)->update('home_featured_links', array('row_number' => 1));
		$this->db->where('order_num >', 5)->update('home_featured_links', array('row_number' => 2));
	}

	public function down()
	{
		$this->dbforge->drop_column('home_featured_links', 'row_number');
	}
}
