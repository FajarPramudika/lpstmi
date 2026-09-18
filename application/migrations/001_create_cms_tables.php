<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tabel konten hasil migrasi WordPress. ID mengikuti ID WordPress
 * (dipakai di kelas CSS post-<ID>, ct-term-<ID>, dan redirect ?p=<ID>).
 */
class Migration_Create_cms_tables extends CI_Migration {

	public function up()
	{
		$opts = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

		$this->db->query("CREATE TABLE authors (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			slug VARCHAR(100) NOT NULL,
			display_name VARCHAR(150) NOT NULL,
			username VARCHAR(60) NULL,
			email VARCHAR(150) NULL,
			password_hash VARCHAR(255) NULL,
			role ENUM('admin','editor') NOT NULL DEFAULT 'editor',
			is_active TINYINT(1) NOT NULL DEFAULT 1,
			last_login_at DATETIME NULL,
			PRIMARY KEY (id),
			UNIQUE KEY uq_authors_slug (slug),
			UNIQUE KEY uq_authors_username (username)
		) $opts");

		$this->db->query("CREATE TABLE terms (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			taxonomy ENUM('category','post_tag') NOT NULL,
			name VARCHAR(200) NOT NULL,
			slug VARCHAR(200) NOT NULL,
			description TEXT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY uq_terms_taxonomy_slug (taxonomy, slug)
		) $opts");

		$this->db->query("CREATE TABLE media (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			file VARCHAR(255) NOT NULL COMMENT 'relatif terhadap wp-content/uploads/',
			width INT UNSIGNED NOT NULL DEFAULT 0,
			height INT UNSIGNED NOT NULL DEFAULT 0,
			alt VARCHAR(255) NOT NULL DEFAULT '',
			mime_type VARCHAR(100) NOT NULL,
			sizes LONGTEXT NULL COMMENT 'JSON: {size:{file,width,height}}',
			created_at DATETIME NOT NULL,
			PRIMARY KEY (id)
		) $opts");

		$this->db->query("CREATE TABLE posts (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			slug VARCHAR(200) NOT NULL,
			title TEXT NOT NULL COMMENT 'HTML',
			content LONGTEXT NOT NULL COMMENT 'HTML, URL situs ditulis sebagai {base_url}',
			excerpt TEXT NOT NULL COMMENT 'HTML, seperti tampil di kartu arsip',
			author_id INT UNSIGNED NOT NULL,
			featured_media_id INT UNSIGNED NULL,
			status ENUM('publish','draft') NOT NULL DEFAULT 'draft',
			published_at DATETIME NOT NULL COMMENT 'waktu lokal Asia/Jakarta',
			modified_at DATETIME NOT NULL,
			layout_head VARCHAR(100) NULL COMMENT 'override varian views/layouts/head/',
			layout_foot VARCHAR(100) NULL COMMENT 'override varian views/layouts/foot/',
			PRIMARY KEY (id),
			UNIQUE KEY uq_posts_slug (slug),
			KEY ix_posts_status_published (status, published_at),
			CONSTRAINT fk_posts_author FOREIGN KEY (author_id) REFERENCES authors (id),
			CONSTRAINT fk_posts_media FOREIGN KEY (featured_media_id) REFERENCES media (id) ON DELETE SET NULL
		) $opts");

		$this->db->query("CREATE TABLE post_terms (
			post_id INT UNSIGNED NOT NULL,
			term_id INT UNSIGNED NOT NULL,
			term_order INT UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (post_id, term_id),
			KEY ix_post_terms_term (term_id),
			CONSTRAINT fk_post_terms_post FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE,
			CONSTRAINT fk_post_terms_term FOREIGN KEY (term_id) REFERENCES terms (id) ON DELETE CASCADE
		) $opts");
	}

	public function down()
	{
		foreach (array('post_terms', 'posts', 'media', 'terms', 'authors') as $table)
		{
			$this->db->query('DROP TABLE IF EXISTS '.$table);
		}
	}
}
