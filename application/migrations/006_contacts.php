<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Contacts extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'`key`' => array(
				'type'       => 'VARCHAR',
				'constraint' => '50',
			),
			'value' => array(
				'type' => 'TEXT',
			),
		));
		$this->dbforge->add_key('key', TRUE);
		$this->dbforge->create_table('contacts', TRUE, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci'));

		// Insert initial data based on WordPress site defaults
		$this->db->insert_batch('contacts', array(
			array('key' => 'email', 'value' => 'humas@stmi.ac.id'),
			array('key' => 'phone', 'value' => '021-42888206'),
			array('key' => 'whatsapp', 'value' => '0851-552-44455'),
			array('key' => 'whatsapp_url', 'value' => 'https://web.whatsapp.com/send?phone=6285155244455'),
		));
	}

	public function down()
	{
		$this->dbforge->drop_table('contacts', TRUE);
	}
}
