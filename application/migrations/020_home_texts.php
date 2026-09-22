<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Teks profil STMI dan tiga blok keunggulan beranda (Pengajar Profesional, Kurikulum Terbaru, Dual System)
 * pindah dari view ke home_options supaya bisa diubah dari admin (tab "Profil & Keunggulan").
 *
 * Nilai awal = teks clone apa adanya (termasuk salah ketik "menerapakan"), jadi beranda tetap byte-identik.
 * Key yang sudah ada tidak ditimpa: migrasi aman dijalankan di database yang sudah diisi seed.
 * Format: profile_text = paragraf dipisah satu baris kosong, feature_3_list = satu butir per baris;
 * tag yang diizinkan hanya yang lolos safe_inline_html() (strong, em, br, ...).
 */
class Migration_Home_texts extends CI_Migration {

	public static function defaults()
	{
		return array(
			'profile_text'    => "Sekolah Tinggi Manajemen Industri (STMI) adalah Perguruan Tinggi Negeri yang berdiri sejak tahun 1968 di bawah binaan Kementerian Perindustrian. Tahun 2014 beralih menjadi Politeknik STMI Jakarta dengan spesialisasi kompetensi pada Industri Otomotif.\n\nPoliteknik STMI Jakarta menyelenggarakan pendidikan dengan jenjang Sarjana Terapan dengan gelar [S.Tr.]. Politeknik STMI Jakarta telah melakukan banyak kerjasama dengan industri, sehingga lulusan Politeknik STMI Jakarta terserap di dunia kerja.",
			'feature_1_title' => 'Pengajar Profesional',
			'feature_1_text'  => 'Tenaga pengajar dengan pengalaman di bidang industri dan praktisi dari Industri',
			'feature_2_title' => 'Kurikulum Terbaru',
			'feature_2_text'  => 'Kami menerapakan kurikulum terbaru sejalan dengan kebutuhan industri',
			'feature_3_title' => 'Dual System',
			'feature_3_text'  => 'Kurikulum Pendidikan Berorientasi <em><strong>Dual System</strong></em>',
			'feature_3_list'  => "5 semester di Kampus\n2 semester di Industri\n1 semester Tugas Akhir",
		);
	}

	public function up()
	{
		foreach (self::defaults() as $key => $value)
		{
			$this->db->query('INSERT IGNORE INTO home_options (`key`, `value`) VALUES (?, ?)', array($key, $value));
		}
	}

	public function down()
	{
		$this->db->where_in('key', array_keys(self::defaults()))->delete('home_options');
	}
}
