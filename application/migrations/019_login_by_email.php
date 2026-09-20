<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Login panel admin boleh memakai email selain username.
 *
 * - authors.email menjadi UNIK. Email kini ikut menjadi identitas login, jadi dua akun tidak boleh
 *   memakai alamat yang sama — kalau tidak, pencarian akun saat login menjadi ambigu.
 *   MySQL mengizinkan banyak baris NULL pada indeks unik, jadi akun tanpa email tetap boleh lebih dari satu.
 * - login_attempts.username dilebarkan ke 190: kolom ini menyimpan apa yang diketik di form login,
 *   dan alamat email bisa lebih panjang dari 60 karakter.
 */
class Migration_Login_by_email extends CI_Migration {

	public function up()
	{
		$this->db->query('ALTER TABLE authors ADD UNIQUE KEY uq_authors_email (email)');
		$this->db->query('ALTER TABLE login_attempts MODIFY username VARCHAR(190) NOT NULL');
	}

	public function down()
	{
		$this->db->query('ALTER TABLE authors DROP INDEX uq_authors_email');
		$this->db->query('ALTER TABLE login_attempts MODIFY username VARCHAR(60) NOT NULL');
	}
}
