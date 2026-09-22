<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_option_model extends CI_Model {

	/**
	 * Teks beranda yang dikelola di tab admin "Profil & Keunggulan" (nilai awal: migrasi 020).
	 * Kolom *_text/profile_text/feature_3_list boleh memuat tag yang lolos safe_inline_html().
	 */
	public static $text_keys = array(
		'profile_text',
		'feature_1_title', 'feature_1_text',
		'feature_2_title', 'feature_2_text',
		'feature_3_title', 'feature_3_text', 'feature_3_list',
	);

	public function get($key, $default = NULL)
	{
		$row = $this->db->get_where('home_options', array('key' => $key))->row_array();
		return $row ? $row['value'] : $default;
	}

	public function get_all()
	{
		$rows = $this->db->get('home_options')->result_array();
		$opts = array();
		foreach ($rows as $row) {
			$opts[$row['key']] = $row['value'];
		}
		return $opts;
	}

	/**
	 * Semua key $text_keys; key yang belum ada di tabel (migrasi 020 belum dijalankan) = string kosong.
	 */
	public function texts()
	{
		return array_merge(array_fill_keys(self::$text_keys, ''),
			array_intersect_key($this->get_all(), array_flip(self::$text_keys)));
	}

	public function set($key, $value)
	{
		$exists = $this->db->get_where('home_options', array('key' => $key))->num_rows() > 0;
		if ($exists) {
			$this->db->update('home_options', array('value' => $value), array('key' => $key));
		} else {
			$this->db->insert('home_options', array('key' => $key, 'value' => $value));
		}
	}
}
