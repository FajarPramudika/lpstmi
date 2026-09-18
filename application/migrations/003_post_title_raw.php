<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * posts.title menyimpan judul mentah (post_title WordPress); tampilan memakai wp_texturize().
 */
class Migration_Post_title_raw extends CI_Migration {

	public function up()
	{
		$this->db->query("ALTER TABLE posts MODIFY title VARCHAR(500) NOT NULL COMMENT 'teks mentah; ditampilkan lewat wp_texturize()'");
	}

	public function down()
	{
		$this->db->query("ALTER TABLE posts MODIFY title TEXT NOT NULL COMMENT 'HTML'");
	}
}
