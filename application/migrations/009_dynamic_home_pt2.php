<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Dynamic_home_pt2 extends CI_Migration {

	public function up()
	{
		$opts = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

		// Tabel home_banners
		$this->db->query("CREATE TABLE home_banners (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			image_path VARCHAR(255) NOT NULL,
			image_srcset VARCHAR(500) NULL,
			image_class VARCHAR(100) NOT NULL DEFAULT 'swiper-slide-image',
			url VARCHAR(255) NULL,
			order_num INT UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (id)
		) $opts");

		// Tabel home_partners (Kerjasama Lembaga dan Perusahaan)
		$this->db->query("CREATE TABLE home_partners (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			image_path VARCHAR(255) NOT NULL,
			image_srcset VARCHAR(500) NULL,
			name VARCHAR(255) NULL,
			url VARCHAR(255) NULL,
			order_num INT UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (id)
		) $opts");

		// Tabel home_options (General Key-Value settings)
		$this->db->query("CREATE TABLE home_options (
			`key` VARCHAR(100) NOT NULL,
			`value` TEXT NULL,
			PRIMARY KEY (`key`)
		) $opts");

		// Insert initial data for home_banners (from the hardcoded HTML)
		$this->db->insert('home_banners', array(
			'image_path' => 'wp-content/uploads/2024/03/Header-Politeknik-STMI.jpg',
			'order_num' => 1
		));

		// Insert initial data for home_options
		$this->db->insert('home_options', array(
			'key' => 'video_url',
			'value' => 'https://youtu.be/kTt11d4Twik'
		));

		// Insert initial data for home_partners (from hardcoded HTML)
		$partners = array(
			'wp-content/uploads/2024/03/Logo-PT.-Mada-Wikri-Tunggal-150x150-1.jpg',
			'wp-content/uploads/2024/03/Logo-PT-Hasura-Mitra-Gemilang-150x150-1.jpg',
			'wp-content/uploads/2024/03/Logo-PT-Bumimulia-Indah-Lestari-1-1-150x150-1.jpg',
			'wp-content/uploads/2024/03/Logo-PT-Autoplastik-Indonesia-150x150-1.jpg',
			'wp-content/uploads/2024/03/Logo-PT-Emblem-Asia-150x150-1.jpg',
			'wp-content/uploads/2024/03/Logo-PT-Injeksi-Plastik-Pasifik-150x150-1.jpg',
			'wp-content/uploads/2024/03/Inti-Ganda-Perdana.jpg'
		);

		$order = 1;
		foreach ($partners as $p) {
			$this->db->insert('home_partners', array(
				'image_path' => $p,
				'order_num' => $order++
			));
		}
	}

	public function down()
	{
		$this->db->query('DROP TABLE IF EXISTS home_banners');
		$this->db->query('DROP TABLE IF EXISTS home_partners');
		$this->db->query('DROP TABLE IF EXISTS home_options');
	}
}
