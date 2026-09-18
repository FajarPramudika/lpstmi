<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Website author (ikon di halaman arsip author).
 */
class Migration_Author_website extends CI_Migration {

	public function up()
	{
		$this->db->query("ALTER TABLE authors ADD website VARCHAR(255) NULL AFTER gravatar_hash");
	}

	public function down()
	{
		$this->db->query('ALTER TABLE authors DROP website');
	}
}
