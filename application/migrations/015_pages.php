<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * page_index -> pages: halaman statis dikelola dari database (admin /admin/pages).
 *
 * - view NULL      : dirender dengan template halaman standar (views/pages/_page.php) dari kolom content.
 * - view terisi    : halaman berkerangka khusus (beranda, statistik, lowongan kerja) yang tetap berupa file view;
 *                    content hanya dipakai pencarian.
 * Isi awal: php index.php tools import_pages
 */
class Migration_Pages extends CI_Migration {

	public function up()
	{
		$this->db->query('RENAME TABLE page_index TO pages');
		$this->db->query("ALTER TABLE pages
			DROP FOREIGN KEY fk_page_index_author,
			DROP FOREIGN KEY fk_page_index_media,
			DROP INDEX uq_page_index_key,
			CHANGE id id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID page WordPress (kelas post-<ID>, page-id-<ID>)',
			CHANGE page_key slug VARCHAR(200) NOT NULL COMMENT 'URL /<slug> (home = beranda)',
			CHANGE content content LONGTEXT NOT NULL COMMENT 'HTML isi entry-content (view NULL) atau konten untuk pencarian (view terisi); URL situs sebagai token {base_url}',
			ADD view VARCHAR(100) NULL DEFAULT NULL COMMENT 'view khusus; NULL = template halaman standar' AFTER content,
			ADD status VARCHAR(20) NOT NULL DEFAULT 'publish' COMMENT 'publish/draft' AFTER featured_media_id,
			ADD layout_head VARCHAR(100) NULL DEFAULT NULL COMMENT 'varian views/layouts/head/ (NULL = otomatis)' AFTER status,
			ADD layout_foot VARCHAR(100) NULL DEFAULT NULL COMMENT 'varian views/layouts/foot/ (NULL = otomatis)' AFTER layout_head,
			ADD UNIQUE KEY uq_pages_slug (slug),
			ADD CONSTRAINT fk_pages_author FOREIGN KEY (author_id) REFERENCES authors (id),
			ADD CONSTRAINT fk_pages_media FOREIGN KEY (featured_media_id) REFERENCES media (id) ON DELETE SET NULL,
			AUTO_INCREMENT = 20000");
	}

	public function down()
	{
		$this->db->query("ALTER TABLE pages
			DROP FOREIGN KEY fk_pages_author,
			DROP FOREIGN KEY fk_pages_media,
			DROP INDEX uq_pages_slug,
			DROP COLUMN view, DROP COLUMN status, DROP COLUMN layout_head, DROP COLUMN layout_foot,
			CHANGE id id INT UNSIGNED NOT NULL COMMENT 'ID page WordPress (kelas post-<ID>)',
			CHANGE slug page_key VARCHAR(200) NOT NULL COMMENT 'kunci di config/pages.php (home = beranda)',
			ADD UNIQUE KEY uq_page_index_key (page_key),
			ADD CONSTRAINT fk_page_index_author FOREIGN KEY (author_id) REFERENCES authors (id),
			ADD CONSTRAINT fk_page_index_media FOREIGN KEY (featured_media_id) REFERENCES media (id) ON DELETE SET NULL");
		$this->db->query('RENAME TABLE pages TO page_index');
	}
}
