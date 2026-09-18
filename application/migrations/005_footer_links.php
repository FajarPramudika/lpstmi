<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Footer_links extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type'           => 'INT',
				'constraint'     => 11,
				'unsigned'       => TRUE,
				'auto_increment' => TRUE
			),
			'title' => array(
				'type'       => 'VARCHAR',
				'constraint' => '255',
			),
			'url' => array(
				'type'       => 'VARCHAR',
				'constraint' => '255',
			),
			'order_num' => array(
				'type'       => 'INT',
				'constraint' => 11,
				'default'    => 0,
			),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('footer_links');

		// Insert default data based on existing static HTML
		$data = array(
			array('title' => 'Kementerian Perindustrian RI', 'url' => '#', 'order_num' => 1),
			array('title' => 'Akreditasi BAN-PT', 'url' => '#', 'order_num' => 2),
			array('title' => 'FB Grup Prodi Sistem Informasi', 'url' => '#', 'order_num' => 3),
			array('title' => 'Pendaftaran Mahasiswa Baru', 'url' => '#', 'order_num' => 4),
			array('title' => 'Tracer Studi Alumni', 'url' => '#', 'order_num' => 5),
			array('title' => 'E-Learning STMI', 'url' => '#', 'order_num' => 6),
			array('title' => 'P2M STMI', 'url' => '#', 'order_num' => 7),
			array('title' => 'FB Group Prodi Manajemen Bisnis Industri', 'url' => '#', 'order_num' => 8),
			array('title' => 'Katalog Perpustakaan Online STMI', 'url' => '#', 'order_num' => 9),
			array('title' => 'Satuan Penjaminan Mutu', 'url' => '#', 'order_num' => 10),
		);
		$this->db->insert_batch('footer_links', $data);
	}

	public function down()
	{
		$this->dbforge->drop_table('footer_links');
	}
}
