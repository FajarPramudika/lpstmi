<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pengerasan login panel admin (audit keamanan 2026-09-20, temuan 3 & 7):
 *
 * - login_attempts: catatan percobaan login gagal per IP dan per username. Sebelumnya hitungan
 *   disimpan di session milik penyerang sendiri, sehingga kuncinya bisa dilewati hanya dengan
 *   membuang cookie. Catatan di database tidak bisa dibuang oleh klien.
 * - authors.password_changed_at: penanda kapan password terakhir diubah. Session menyimpan nilai ini
 *   saat login; kalau berbeda, session ditolak. Dengan begitu mengganti password mematikan sesi lain.
 *   NULL = belum pernah diganti (sesi lama tetap berlaku, jadi migrasi ini tidak mengusir siapa pun).
 */
class Migration_Login_security extends CI_Migration {

	public function up()
	{
		$this->db->query('CREATE TABLE IF NOT EXISTS login_attempts (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			ip VARBINARY(16) NOT NULL COMMENT "hasil inet_pton(); mendukung IPv4 & IPv6",
			username VARCHAR(60) NOT NULL,
			attempted_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			KEY ip_time (ip, attempted_at),
			KEY user_time (username, attempted_at)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');

		$this->db->query('ALTER TABLE authors ADD password_changed_at DATETIME NULL DEFAULT NULL
			COMMENT "diisi saat password diubah; mematikan sesi lain" AFTER password_hash');
	}

	public function down()
	{
		$this->db->query('DROP TABLE IF EXISTS login_attempts');
		$this->db->query('ALTER TABLE authors DROP COLUMN password_changed_at');
	}
}
