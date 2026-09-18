<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Dynamic_home_pt1 extends CI_Migration {

	public function up()
	{
		// Table for featured links
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'container_id' => array('type' => 'VARCHAR', 'constraint' => '20'),
			'widget_id'    => array('type' => 'VARCHAR', 'constraint' => '20'),
			'url'          => array('type' => 'VARCHAR', 'constraint' => '255'),
			'image_path'   => array('type' => 'VARCHAR', 'constraint' => '255'),
			'image_srcset' => array('type' => 'TEXT', 'null' => TRUE),
			'image_class'  => array('type' => 'VARCHAR', 'constraint' => '255'),
			'img_hint_key' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE),
			'order_num'    => array('type' => 'INT', 'default' => 0),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('home_featured_links', TRUE);

		// Insert default data for featured links
		$featured_links = array(
			array('container_id' => '5fda677', 'widget_id' => 'e1a2c9b', 'url' => 'http://e-learning.stmi.ac.id/', 'image_path' => 'wp-content/uploads/2024/03/E-Learning-STMI.png', 'image_srcset' => 'wp-content/uploads/2024/03/E-Learning-STMI.png 348w, wp-content/uploads/2024/03/E-Learning-STMI-300x77.png 300w', 'image_class' => 'attachment-full size-full wp-image-724', 'img_hint_key' => null, 'order_num' => 1),
			array('container_id' => 'd587fad', 'widget_id' => '3640020', 'url' => 'http://jarvis.stmi.ac.id/', 'image_path' => 'wp-content/uploads/2024/03/Jarvis-STMI.png', 'image_srcset' => 'wp-content/uploads/2024/03/Jarvis-STMI.png 348w, wp-content/uploads/2024/03/Jarvis-STMI-300x77.png 300w', 'image_class' => 'attachment-full size-full wp-image-723', 'img_hint_key' => 'home:0', 'order_num' => 2),
			array('container_id' => 'd8a9667', 'widget_id' => '0387f7d', 'url' => 'http://spm.stmi.ac.id/', 'image_path' => 'wp-content/uploads/2024/03/SPM-STMI.png', 'image_srcset' => 'wp-content/uploads/2024/03/SPM-STMI.png 348w, wp-content/uploads/2024/03/SPM-STMI-300x77.png 300w', 'image_class' => 'attachment-full size-full wp-image-726', 'img_hint_key' => 'home:1', 'order_num' => 3),
			array('container_id' => '1858478', 'widget_id' => 'adce476', 'url' => 'http://p2m.stmi.ac.id/', 'image_path' => 'wp-content/uploads/2024/03/P2M-STMI.png', 'image_srcset' => 'wp-content/uploads/2024/03/P2M-STMI.png 348w, wp-content/uploads/2024/03/P2M-STMI-300x77.png 300w', 'image_class' => 'attachment-full size-full wp-image-727', 'img_hint_key' => 'home:2', 'order_num' => 4),
			array('container_id' => '264a112', 'widget_id' => 'a382c05', 'url' => 'https://karir.stmi.ac.id/', 'image_path' => 'wp-content/uploads/2024/03/Career-STMI.png', 'image_srcset' => 'wp-content/uploads/2024/03/Career-STMI.png 348w, wp-content/uploads/2024/03/Career-STMI-300x77.png 300w', 'image_class' => 'attachment-full size-full wp-image-731', 'img_hint_key' => 'home:3', 'order_num' => 5),
			
			array('container_id' => '1a9ae2c', 'widget_id' => '06459aa', 'url' => 'http://spi.stmi.ac.id/', 'image_path' => 'wp-content/uploads/2024/03/SPI-STMI.png', 'image_srcset' => 'wp-content/uploads/2024/03/SPI-STMI.png 348w, wp-content/uploads/2024/03/SPI-STMI-300x77.png 300w', 'image_class' => 'attachment-full size-full wp-image-735', 'img_hint_key' => 'home:4', 'order_num' => 6),
			array('container_id' => '881f224', 'widget_id' => '0f8fbb2', 'url' => 'http://lsp.stmi.ac.id/', 'image_path' => 'wp-content/uploads/2025/07/Add-a-heading-5.png', 'image_srcset' => null, 'image_class' => 'attachment-full size-full wp-image-2857', 'img_hint_key' => 'home:5', 'order_num' => 7),
			array('container_id' => '18779a0', 'widget_id' => 'd917188', 'url' => 'http://lib.stmi.ac.id/', 'image_path' => 'wp-content/uploads/2024/03/Perpustakaan-STMI.png', 'image_srcset' => 'wp-content/uploads/2024/03/Perpustakaan-STMI.png 348w, wp-content/uploads/2024/03/Perpustakaan-STMI-300x77.png 300w', 'image_class' => 'attachment-full size-full wp-image-740', 'img_hint_key' => 'home:6', 'order_num' => 8),
			array('container_id' => 'c2d66d6', 'widget_id' => '71418fb', 'url' => 'http://virtualtour.stmi.ac.id/', 'image_path' => 'wp-content/uploads/2024/03/Virtual-Tour-STMI.png', 'image_srcset' => 'wp-content/uploads/2024/03/Virtual-Tour-STMI.png 348w, wp-content/uploads/2024/03/Virtual-Tour-STMI-300x77.png 300w', 'image_class' => 'attachment-full size-full wp-image-744', 'img_hint_key' => 'home:7', 'order_num' => 9),
			array('container_id' => '1410334', 'widget_id' => '509c686', 'url' => 'https://span.lapor.go.id/', 'image_path' => 'wp-content/uploads/2024/03/Pengaduan-STMI.png', 'image_srcset' => 'wp-content/uploads/2024/03/Pengaduan-STMI.png 348w, wp-content/uploads/2024/03/Pengaduan-STMI-300x77.png 300w', 'image_class' => 'attachment-full size-full wp-image-748', 'img_hint_key' => 'home:8', 'order_num' => 10),
			array('container_id' => '92f247a', 'widget_id' => '3f77ebf', 'url' => 'https://ppid.stmi.ac.id/layanan/pengaduan_gratifikasi', 'image_path' => 'wp-content/uploads/2024/03/Gratifikasi-STMI.png', 'image_srcset' => 'wp-content/uploads/2024/03/Gratifikasi-STMI.png 348w, wp-content/uploads/2024/03/Gratifikasi-STMI-300x77.png 300w', 'image_class' => 'attachment-full size-full wp-image-750', 'img_hint_key' => 'home:9', 'order_num' => 11),
		);
		$this->db->insert_batch('home_featured_links', $featured_links);


		// Table for study programs
		$this->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'container_id' => array('type' => 'VARCHAR', 'constraint' => '20'),
			'icon_id'      => array('type' => 'VARCHAR', 'constraint' => '20'),
			'heading_id'   => array('type' => 'VARCHAR', 'constraint' => '20'),
			'text_id'      => array('type' => 'VARCHAR', 'constraint' => '20'),
			'button_id'    => array('type' => 'VARCHAR', 'constraint' => '20'),
			
			'title'        => array('type' => 'VARCHAR', 'constraint' => '255'),
			'description'  => array('type' => 'TEXT'),
			'url'          => array('type' => 'VARCHAR', 'constraint' => '255'),
			'icon_svg'     => array('type' => 'TEXT'),
			'order_num'    => array('type' => 'INT', 'default' => 0),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('home_study_programs', TRUE);

		// Insert default data for study programs
		$study_programs = array(
			array(
				'container_id' => '64b9fa9', 'icon_id' => 'c18b7b4', 'heading_id' => 'd90cccf', 'text_id' => 'e8a9d7e', 'button_id' => '7442f68',
				'title' => 'Teknik Industri <br> Otomotif',
				'description' => 'Unggul dan terdepan dalam Teknik Industri Otomotif, serta memimpin dalam pemenuhan tenaga kerja di sektor otomotif regional dan nasional.',
				'url' => 'https://tio.stmi.ac.id/',
				'icon_svg' => '<path d="M512.1 191l-8.2 14.3c-3 5.3-9.4 7.5-15.1 5.4-11.8-4.4-22.6-10.7-32.1-18.6-4.6-3.8-5.8-10.5-2.8-15.7l8.2-14.3c-6.9-8-12.3-17.3-15.9-27.4h-16.5c-6 0-11.2-4.3-12.2-10.3-2-12-2.1-24.6 0-37.1 1-6 6.2-10.4 12.2-10.4h16.5c3.6-10.1 9-19.4 15.9-27.4l-8.2-14.3c-3-5.2-1.9-11.9 2.8-15.7 9.5-7.9 20.4-14.2 32.1-18.6 5.7-2.1 12.1.1 15.1 5.4l8.2 14.3c10.5-1.9 21.2-1.9 31.7 0L552 6.3c3-5.3 9.4-7.5 15.1-5.4 11.8 4.4 22.6 10.7 32.1 18.6 4.6 3.8 5.8 10.5 2.8 15.7l-8.2 14.3c6.9 8 12.3 17.3 15.9 27.4h16.5c6 0 11.2 4.3 12.2 10.3 2 12 2.1 24.6 0 37.1-1 6-6.2 10.4-12.2 10.4h-16.5c-3.6 10.1-9 19.4-15.9 27.4l8.2 14.3c3 5.2 1.9 11.9-2.8 15.7-9.5 7.9-20.4 14.2-32.1 18.6-5.7 2.1-12.1-.1-15.1-5.4l-8.2-14.3c-10.4 1.9-21.2 1.9-31.7 0zm-10.5-58.8c38.5 29.6 82.4-14.3 52.8-52.8-38.5-29.7-82.4 14.3-52.8 52.8zM386.3 286.1l33.7 16.8c10.1 5.8 14.5 18.1 10.5 29.1-8.9 24.2-26.4 46.4-42.6 65.8-7.4 8.9-20.2 11.1-30.3 5.3l-29.1-16.8c-16 13.7-34.6 24.6-54.9 31.7v33.6c0 11.6-8.3 21.6-19.7 23.6-24.6 4.2-50.4 4.4-75.9 0-11.5-2-20-11.9-20-23.6V418c-20.3-7.2-38.9-18-54.9-31.7L74 403c-10 5.8-22.9 3.6-30.3-5.3-16.2-19.4-33.3-41.6-42.2-65.7-4-10.9.4-23.2 10.5-29.1l33.3-16.8c-3.9-20.9-3.9-42.4 0-63.4L12 205.8c-10.1-5.8-14.6-18.1-10.5-29 8.9-24.2 26-46.4 42.2-65.8 7.4-8.9 20.2-11.1 30.3-5.3l29.1 16.8c16-13.7 34.6-24.6 54.9-31.7V57.1c0-11.5 8.2-21.5 19.6-23.5 24.6-4.2 50.5-4.4 76-.1 11.5 2 20 11.9 20 23.6v33.6c20.3 7.2 38.9 18 54.9 31.7l29.1-16.8c10-5.8 22.9-3.6 30.3 5.3 16.2 19.4 33.2 41.6 42.1 65.8 4 10.9.1 23.2-10 29.1l-33.7 16.8c3.9 21 3.9 42.5 0 63.5zm-117.6 21.1c59.2-77-28.7-164.9-105.7-105.7-59.2 77 28.7 164.9 105.7 105.7zm243.4 182.7l-8.2 14.3c-3 5.3-9.4 7.5-15.1 5.4-11.8-4.4-22.6-10.7-32.1-18.6-4.6-3.8-5.8-10.5-2.8-15.7l8.2-14.3c-6.9-8-12.3-17.3-15.9-27.4h-16.5c-6 0-11.2-4.3-12.2-10.3-2-12-2.1-24.6 0-37.1 1-6 6.2-10.4 12.2-10.4h16.5c3.6-10.1 9-19.4 15.9-27.4l-8.2-14.3c-3-5.2-1.9-11.9 2.8-15.7 9.5-7.9 20.4-14.2 32.1-18.6 5.7-2.1 12.1.1 15.1 5.4l8.2 14.3c10.5-1.9 21.2-1.9 31.7 0l8.2-14.3c3-5.3 9.4-7.5 15.1-5.4 11.8 4.4 22.6 10.7 32.1 18.6 4.6 3.8 5.8 10.5 2.8 15.7l-8.2 14.3c6.9 8 12.3 17.3 15.9 27.4h16.5c6 0 11.2 4.3 12.2 10.3 2 12 2.1 24.6 0 37.1-1 6-6.2 10.4-12.2 10.4h-16.5c-3.6 10.1-9 19.4-15.9 27.4l8.2 14.3c3 5.2 1.9 11.9-2.8 15.7-9.5 7.9-20.4 14.2-32.1 18.6-5.7 2.1-12.1-.1-15.1-5.4l-8.2-14.3c-10.4 1.9-21.2 1.9-31.7 0zM501.6 431c38.5 29.6 82.4-14.3 52.8-52.8-38.5-29.6-82.4 14.3-52.8 52.8z"></path>',
				'order_num' => 1
			),
			array(
				'container_id' => 'f049439', 'icon_id' => 'ad6679a', 'heading_id' => '69cbda6', 'text_id' => 'baa634f', 'button_id' => '670ff96',
				'title' => 'Teknik Kimia Polimer',
				'description' => 'Menjadi pelopor pendidikan Teknik Kimia berbasis polimer untuk menghasilkan tenaga kerja unggul dan berdaya saing global di industri otomotif.',
				'url' => 'https://tkp.stmi.ac.id/',
				'icon_svg' => '<path d="M477.7 186.1L309.5 18.3c-3.1-3.1-8.2-3.1-11.3 0l-34 33.9c-3.1 3.1-3.1 8.2 0 11.3l11.2 11.1L33 316.5c-38.8 38.7-45.1 102-9.4 143.5 20.6 24 49.5 36 78.4 35.9 26.4 0 52.8-10 72.9-30.1l246.3-245.7 11.2 11.1c3.1 3.1 8.2 3.1 11.3 0l34-33.9c3.1-3 3.1-8.1 0-11.2zM318 256H161l148-147.7 78.5 78.3L318 256z"></path>',
				'order_num' => 2
			),
			array(
				'container_id' => '7f98178', 'icon_id' => '57f9592', 'heading_id' => '4f06a0c', 'text_id' => '6d47d8e', 'button_id' => '7a5982b',
				'title' => 'Sistem Informasi Industri Otomotif',
				'description' => 'Penyedia Sumber Daya Manusia di bidang Sistem Informasi yang kompeten dan unggul untuk sektor industri dalam negeri.',
				'url' => 'https://siio.stmi.ac.id/',
				'icon_svg' => '<path d="M255.03 261.65c6.25 6.25 16.38 6.25 22.63 0l11.31-11.31c6.25-6.25 6.25-16.38 0-22.63L253.25 192l35.71-35.72c6.25-6.25 6.25-16.38 0-22.63l-11.31-11.31c-6.25-6.25-16.38-6.25-22.63 0l-58.34 58.34c-6.25 6.25-6.25 16.38 0 22.63l58.35 58.34zm96.01-11.3l11.31 11.31c6.25 6.25 16.38 6.25 22.63 0l58.34-58.34c6.25-6.25 6.25-16.38 0-22.63l-58.34-58.34c-6.25-6.25-16.38-6.25-22.63 0l-11.31 11.31c-6.25 6.25-6.25 16.38 0 22.63L386.75 192l-35.71 35.72c-6.25 6.25-6.25 16.38 0 22.63zM624 416H381.54c-.74 19.81-14.71 32-32.74 32H288c-18.69 0-33.02-17.47-32.77-32H16c-8.8 0-16 7.2-16 16v16c0 35.2 28.8 64 64 64h512c35.2 0 64-28.8 64-64v-16c0-8.8-7.2-16-16-16zM576 48c0-26.4-21.6-48-48-48H112C85.6 0 64 21.6 64 48v336h512V48zm-64 272H128V64h384v256z"></path>',
				'order_num' => 3
			),
			array(
				'container_id' => '29352f3', 'icon_id' => '84ab0a9', 'heading_id' => 'b02f143', 'text_id' => '2a44ec3', 'button_id' => '30eb295',
				'title' => 'Administrasi Bisnis Otomotif',
				'description' => 'Menjadi pelopor unit pendidikan menghasilkan tenaga kerja unggul di bidang administrasi bisnis industri otomotif.',
				'url' => 'https://abo.stmi.ac.id/',
				'icon_svg' => '<path d="M400 0H48C22.4 0 0 22.4 0 48v416c0 25.6 22.4 48 48 48h352c25.6 0 48-22.4 48-48V48c0-25.6-22.4-48-48-48zM128 435.2c0 6.4-6.4 12.8-12.8 12.8H76.8c-6.4 0-12.8-6.4-12.8-12.8v-38.4c0-6.4 6.4-12.8 12.8-12.8h38.4c6.4 0 12.8 6.4 12.8 12.8v38.4zm0-128c0 6.4-6.4 12.8-12.8 12.8H76.8c-6.4 0-12.8-6.4-12.8-12.8v-38.4c0-6.4 6.4-12.8 12.8-12.8h38.4c6.4 0 12.8 6.4 12.8 12.8v38.4zm128 128c0 6.4-6.4 12.8-12.8 12.8h-38.4c-6.4 0-12.8-6.4-12.8-12.8v-38.4c0-6.4 6.4-12.8 12.8-12.8h38.4c6.4 0 12.8 6.4 12.8 12.8v38.4zm0-128c0 6.4-6.4 12.8-12.8 12.8h-38.4c-6.4 0-12.8-6.4-12.8-12.8v-38.4c0-6.4 6.4-12.8 12.8-12.8h38.4c6.4 0 12.8 6.4 12.8 12.8v38.4zm128 128c0 6.4-6.4 12.8-12.8 12.8h-38.4c-6.4 0-12.8-6.4-12.8-12.8V268.8c0-6.4 6.4-12.8 12.8-12.8h38.4c6.4 0 12.8 6.4 12.8 12.8v166.4zm0-256c0 6.4-6.4 12.8-12.8 12.8H76.8c-6.4 0-12.8-6.4-12.8-12.8V76.8C64 70.4 70.4 64 76.8 64h294.4c6.4 0 12.8 6.4 12.8 12.8v102.4z"></path>',
				'order_num' => 4
			),
			array(
				'container_id' => 'bd762c0', 'icon_id' => '54f1231', 'heading_id' => '7fff532', 'text_id' => '2e95780', 'button_id' => '97a47f8',
				'title' => 'Teknologi Rekayasa Otomotif',
				'description' => 'Menjadi program vokasional terdepan yang menghasilkan tenaga kerja unggul dalam bidang desain dan teknologi manufaktur tooling otomotif',
				'url' => 'https://tro.stmi.ac.id/',
				'icon_svg' => '<path d="M223.99908,224a32,32,0,1,0,32.00782,32A32.06431,32.06431,0,0,0,223.99908,224Zm214.172-96c-10.877-19.5-40.50979-50.75-116.27544-41.875C300.39168,34.875,267.63386,0,223.99908,0s-76.39066,34.875-97.89653,86.125C50.3369,77.375,20.706,108.5,9.82907,128-6.54984,157.375-5.17484,201.125,34.958,256-5.17484,310.875-6.54984,354.625,9.82907,384c29.13087,52.375,101.64652,43.625,116.27348,41.875C147.60842,477.125,180.36429,512,223.99908,512s76.3926-34.875,97.89652-86.125c14.62891,1.75,87.14456,10.5,116.27544-41.875C454.55,354.625,453.175,310.875,413.04017,256,453.175,201.125,454.55,157.375,438.171,128ZM63.33886,352c-4-7.25-.125-24.75,15.00391-48.25,6.87695,6.5,14.12891,12.875,21.88087,19.125,1.625,13.75,4,27.125,6.75,40.125C82.34472,363.875,67.09081,358.625,63.33886,352Zm36.88478-162.875c-7.752,6.25-15.00392,12.625-21.88087,19.125-15.12891-23.5-19.00392-41-15.00391-48.25,3.377-6.125,16.37891-11.5,37.88478-11.5,1.75,0,3.875.375,5.75.375C104.09864,162.25,101.84864,175.625,100.22364,189.125ZM223.99908,64c9.50195,0,22.25586,13.5,33.88282,37.25-11.252,3.75-22.50391,8-33.88282,12.875-11.377-4.875-22.62892-9.125-33.88283-12.875C201.74516,77.5,214.49712,64,223.99908,64Zm0,384c-9.502,0-22.25392-13.5-33.88283-37.25,11.25391-3.75,22.50587-8,33.88283-12.875C235.378,402.75,246.62994,407,257.8819,410.75,246.25494,434.5,233.501,448,223.99908,448Zm0-112a80,80,0,1,1,80-80A80.00023,80.00023,0,0,1,223.99908,336ZM384.6593,352c-3.625,6.625-19.00392,11.875-43.63479,11,2.752-13,5.127-26.375,6.752-40.125,7.75195-6.25,15.00391-12.625,21.87891-19.125C384.7843,327.25,388.6593,344.75,384.6593,352ZM369.65538,208.25c-6.875-6.5-14.127-12.875-21.87891-19.125-1.625-13.5-3.875-26.875-6.752-40.25,1.875,0,4.002-.375,5.752-.375,21.50391,0,34.50782,5.375,37.88283,11.5C388.6593,167.25,384.7843,184.75,369.65538,208.25Z"></path>',
				'order_num' => 5
			),
		);
		$this->db->insert_batch('home_study_programs', $study_programs);
	}

	public function down()
	{
		$this->dbforge->drop_table('home_featured_links', TRUE);
		$this->dbforge->drop_table('home_study_programs', TRUE);
	}
}
