<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ikon kartu download ([wpdm_package id='N']): NULL = otomatis dari ekstensi file (web.svg untuk URL luar).
 * Diisi untuk paket yang ikonnya di WordPress berbeda dari aturan otomatis (mis. Google Drive berisi PDF).
 */
class Migration_Download_icon extends CI_Migration {

	public function up()
	{
		$this->db->query("ALTER TABLE downloads ADD icon VARCHAR(20) NULL DEFAULT NULL COMMENT 'nama ikon file-type-icons/<icon>.svg; NULL = otomatis' AFTER file_size");
	}

	public function down()
	{
		$this->db->query('ALTER TABLE downloads DROP COLUMN icon');
	}
}
