<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Menu utama situs (header desktop + menu mobile). ID mengikuti ID item menu WordPress
 * karena dipakai di kelas CSS menu-item-<ID>. Kedalaman tidak dibatasi.
 */
class Migration_Menu_items extends CI_Migration {

	public function up()
	{
		$this->db->query("CREATE TABLE menu_items (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			parent_id INT UNSIGNED NULL,
			position INT UNSIGNED NOT NULL DEFAULT 0,
			title VARCHAR(255) NOT NULL COMMENT 'teks label; ditampilkan lewat wp_texturize()',
			type ENUM('page','category','custom') NOT NULL,
			object_id INT UNSIGNED NULL COMMENT 'page: ID page WordPress (kelas page-item-ID); category: terms.id',
			slug VARCHAR(200) NULL COMMENT 'page: slug halaman statis (kosong = beranda)',
			url VARCHAR(500) NULL COMMENT 'custom: URL (token {base_url} untuk situs ini); NULL = label tanpa link',
			PRIMARY KEY (id),
			KEY ix_menu_items_parent (parent_id, position),
			CONSTRAINT fk_menu_items_parent FOREIGN KEY (parent_id) REFERENCES menu_items (id) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

		// Isi awal = menu "Menu Utama" WordPress (urutan induk sebelum anak).
		$rows = array(
			array(1794, NULL, 1, 'HOME', 'page', 490, '', NULL),
			array(1795, NULL, 2, 'PROFILE', 'custom', NULL, NULL, NULL),
			array(1816, 1795, 1, 'SEJARAH KAMPUS', 'page', 778, 'sejarah-kampus', NULL),
			array(1822, 1795, 2, 'VISI DAN MISI', 'page', 786, 'visi-dan-misi', NULL),
			array(1796, 1795, 3, 'AKADEMIK', 'page', 794, 'akademik', NULL),
			array(1820, 1795, 4, 'TUPOKSI', 'page', 797, 'tupoksi', NULL),
			array(1824, 1795, 5, 'AKREDITASI', 'custom', NULL, NULL, '#'),
			array(1798, 1824, 1, 'SEKOLAH TINGGI MANAJEMEN INDUSTRI', 'page', 1466, 'akreditasi-sekolah-tinggi-manajemen-industri', NULL),
			array(1797, 1824, 2, 'POLITEKNIK STMI JAKARTA', 'page', 801, 'akreditasi-politeknik-stmi', NULL),
			array(1819, 1795, 6, 'STRUKTUR ORGANISASI', 'page', 804, 'struktur-organsasi', NULL),
			array(1813, 1795, 7, 'PROFILE PEJABAT', 'page', 811, 'profile-pejabat', NULL),
			array(1805, 1795, 8, 'LOKASI KAMPUS', 'page', 951, 'lokasi-kampus', NULL),
			array(1802, 1795, 9, 'KEANGGOTAAN SENAT', 'page', 813, 'keanggotaan-senat', NULL),
			array(1818, 1795, 10, 'STATISTIK', 'page', 907, 'statistik', NULL),
			array(1825, NULL, 3, 'PROGRAM STUDI', 'custom', NULL, NULL, '#'),
			array(1826, 1825, 1, 'TEKNIK INDUSTRI OTOMOTIF', 'custom', NULL, NULL, 'http://tio.stmi.ac.id/'),
			array(1827, 1825, 2, 'SISTEM INFORMASI INDUSTRI OTOMOTIF', 'custom', NULL, NULL, 'http://siio.stmi.ac.id/'),
			array(1828, 1825, 3, 'ADMINISTRASI BISNIS OTOMOTIF', 'custom', NULL, NULL, 'http://abo.stmi.ac.id/'),
			array(1829, 1825, 4, 'TEKNIK KIMIA POLIMER', 'custom', NULL, NULL, 'http://tkp.stmi.ac.id/'),
			array(1830, 1825, 5, 'TEKNOLOGI REKAYASA OTOMOTIF', 'custom', NULL, NULL, 'http://tro.stmi.ac.id/'),
			array(1831, 1825, 6, 'TENAGA PENYULUH LAPANGAN', 'custom', NULL, NULL, NULL),
			array(1832, NULL, 4, 'BERITA', 'custom', NULL, NULL, NULL),
			array(1834, 1832, 1, 'BERITA KAMPUS', 'category', 22, NULL, NULL),
			array(1833, 1832, 2, 'PENGUMUMAN KAMPUS', 'category', 1, NULL, NULL),
			array(2727, 1832, 3, 'ARTIKEL', 'category', 45, NULL, NULL),
			array(1835, 1832, 4, 'LOWONGAN KERJA', 'custom', NULL, NULL, '{base_url}lowongan-kerja'),
			array(1836, NULL, 5, 'PELAYANAN PUBLIK', 'custom', NULL, NULL, NULL),
			array(1799, 1836, 1, 'DAFTAR INFORMASI', 'page', 957, 'daftar-informasi', NULL),
			array(1806, 1836, 2, 'MAKLUMAT PELAYANAN', 'page', 959, 'maklumat-pelayanan', NULL),
			array(1837, 1836, 3, 'PERINGATAN DINI', 'custom', NULL, NULL, NULL),
			array(1817, 1836, 4, 'SOP', 'page', 961, 'sop', NULL),
			array(1808, 1836, 5, 'MOU', 'page', 966, 'mou', NULL),
			array(1810, 1836, 6, 'PENETAPAN STANDAR PELAYANAN', 'page', 969, 'penetapan-standar-pelayanan', NULL),
			array(1801, 1836, 7, 'HASIL SURVEY KEPUASAN', 'page', 972, 'hasil-survey-kepuasan', NULL),
			array(1800, 1836, 8, 'HASIL INDEKS PERSEPSI KORUPSI', 'page', 975, 'hasil-indeks-persepsi-korupsi', NULL),
			array(1807, 1836, 9, 'MATERI WORKSHOP', 'page', 977, 'materi-workshop', NULL),
			array(1809, 1836, 10, 'PEDOMAN AKADEMIK DAN NON AKADEMIK', 'page', 979, 'pedoman-akademik-dan-non-akademik', NULL),
			array(3038, 1836, 11, 'LAPORAN KEUANGAN', 'page', 3024, 'laporan-keuangan', NULL),
			array(1838, NULL, 6, 'PERATURAN', 'custom', NULL, NULL, NULL),
			array(1811, 1838, 1, 'PERATURAN', 'page', 997, 'peraturan', NULL),
			array(1823, 1838, 2, 'ZONA INTEGRITAS', 'page', 999, 'zona-integritas', NULL),
			array(1815, 1838, 3, 'RENCANA STRATEGIS', 'page', 1002, 'rencana-strategis', NULL),
			array(1814, 1838, 4, 'RENCANA KINERJA', 'page', 1004, 'rencana-kinerja', NULL),
			array(1812, 1838, 5, 'PERKIN', 'page', 1006, 'perkin', NULL),
			array(1803, 1838, 6, 'LAKIP', 'page', 1008, 'lakip', NULL),
			array(1804, 1838, 7, 'LAPORAN TRI WULAN', 'page', 1010, 'laporan-tri-wulan', NULL),
			array(1821, NULL, 7, 'UNIT MAHASISWA', 'page', 825, 'unit-mahasiswa', NULL),
		);
		foreach ($rows as $r)
		{
			$this->db->insert('menu_items', array_combine(
				array('id', 'parent_id', 'position', 'title', 'type', 'object_id', 'slug', 'url'),
				$r
			));
		}
	}

	public function down()
	{
		$this->db->query('DROP TABLE IF EXISTS menu_items');
	}
}
