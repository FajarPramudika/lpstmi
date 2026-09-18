<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Data author yang tampil di halaman arsip author (/author/<slug>).
 */
class Migration_Author_profile extends CI_Migration {

	public function up()
	{
		$this->db->query("ALTER TABLE authors
			ADD registered_at DATETIME NULL COMMENT 'tampil sebagai Joined: dd/mm/yyyy' AFTER display_name,
			ADD gravatar_hash CHAR(64) NULL COMMENT 'sha256(lowercase email), untuk avatar Gravatar' AFTER registered_at,
			ADD post_count_offset INT NOT NULL DEFAULT 0 COMMENT 'selisih jumlah Articles di WordPress (menghitung tipe konten lain)' AFTER gravatar_hash");
	}

	public function down()
	{
		$this->db->query('ALTER TABLE authors DROP registered_at, DROP gravatar_hash, DROP post_count_offset');
	}
}
