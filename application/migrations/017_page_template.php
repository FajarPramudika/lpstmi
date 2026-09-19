<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Template halaman: default (hero + judul + sidebar, views/pages/_page.php) atau full-width
 * (template WordPress "Elementor Full Width" / elementor_header_footer: hanya isi di dalam <main>, views/pages/_page_full.php).
 */
class Migration_Page_template extends CI_Migration {

	public function up()
	{
		$this->db->query("ALTER TABLE pages ADD template VARCHAR(30) NOT NULL DEFAULT 'default' COMMENT 'default | full-width' AFTER view");
	}

	public function down()
	{
		$this->db->query('ALTER TABLE pages DROP COLUMN template');
	}
}
