<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Item menu utama yang menunjuk ke kategori (ID menu-item WordPress), untuk menandai menu aktif
| di halaman post dan arsip kategori. Lihat views/layouts/partials/header.php.
*/
$config['menu_category_items'] = array(
	'pengumuman'    => 1833,
	'berita-kampus' => 1834,
	'article'       => 2727,
);

// Induk item-item kategori di atas (menu "BERITA").
$config['menu_category_parent'] = 1832;

// Akhiran <title> semua halaman.
$config['site_title'] = 'Politeknik STMI Jakarta';
