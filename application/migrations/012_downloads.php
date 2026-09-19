<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Paket Download Manager (tipe konten wpdmpro WordPress). ID mengikuti ID WordPress
 * (dipakai di ?wpdmdl=<ID> dan kelas CSS post-<ID>).
 */
class Migration_Downloads extends CI_Migration {

	public function up()
	{
		$this->db->query("CREATE TABLE downloads (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			slug VARCHAR(200) NOT NULL COMMENT 'URL /download/<slug>',
			title VARCHAR(500) NOT NULL COMMENT 'teks mentah; ditampilkan lewat wp_texturize()',
			description LONGTEXT NOT NULL COMMENT 'HTML (biasanya embed PDF), URL situs sebagai token {base_url}',
			template ENUM('simplified','default') NOT NULL DEFAULT 'simplified' COMMENT 'template WPDM: Default Template (Simplified) / Default Template',
			button_label VARCHAR(255) NULL COMMENT 'teks tombol; NULL = Download',
			file VARCHAR(500) NOT NULL COMMENT 'relatif wp-content/uploads/, atau URL luar (mis. Google Drive)',
			file_size VARCHAR(50) NOT NULL DEFAULT '' COMMENT 'label ukuran seperti WPDM, mis. 473 KB / 7.32 MB',
			download_count INT UNSIGNED NOT NULL DEFAULT 0,
			author_id INT UNSIGNED NOT NULL,
			featured_media_id INT UNSIGNED NULL,
			status ENUM('publish','draft') NOT NULL DEFAULT 'draft',
			published_at DATETIME NOT NULL COMMENT 'Create Date',
			modified_at DATETIME NOT NULL COMMENT 'Last Updated',
			PRIMARY KEY (id),
			UNIQUE KEY uq_downloads_slug (slug),
			CONSTRAINT fk_downloads_author FOREIGN KEY (author_id) REFERENCES authors (id),
			CONSTRAINT fk_downloads_media FOREIGN KEY (featured_media_id) REFERENCES media (id) ON DELETE SET NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
	}

	public function down()
	{
		$this->db->query('DROP TABLE IF EXISTS downloads');
	}
}
