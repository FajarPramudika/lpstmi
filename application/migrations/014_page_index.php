<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Data halaman statis untuk pencarian (halaman statis sendiri tetap berupa view di views/pages/).
 * Diisi oleh: php index.php tools import_page_index
 */
class Migration_Page_index extends CI_Migration {

	public function up()
	{
		$this->db->query("CREATE TABLE page_index (
			id INT UNSIGNED NOT NULL COMMENT 'ID page WordPress (kelas post-<ID>)',
			page_key VARCHAR(200) NOT NULL COMMENT 'kunci di config/pages.php (home = beranda)',
			title VARCHAR(500) NOT NULL COMMENT 'teks mentah; ditampilkan lewat wp_texturize()',
			content LONGTEXT NOT NULL COMMENT 'HTML konten (untuk dicari & excerpt), URL situs sebagai token {base_url}',
			author_id INT UNSIGNED NOT NULL,
			featured_media_id INT UNSIGNED NULL,
			published_at DATETIME NOT NULL,
			modified_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY uq_page_index_key (page_key),
			CONSTRAINT fk_page_index_author FOREIGN KEY (author_id) REFERENCES authors (id),
			CONSTRAINT fk_page_index_media FOREIGN KEY (featured_media_id) REFERENCES media (id) ON DELETE SET NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
	}

	public function down()
	{
		$this->db->query('DROP TABLE IF EXISTS page_index');
	}
}
