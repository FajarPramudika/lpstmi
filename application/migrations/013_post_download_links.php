<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tombol Download di konten post (embed WPDM) masih memakai path HTTrack relatif
 * (data-downloadurl="../download/<slug>/index<hash>.html?wpdmdl=ID..."). Ubah ke URL situs
 * ({base_url}download/<slug>?wpdmdl=ID...) dengan pemetaan yang sama seperti converter (Wp_clone).
 * Hanya atribut data-downloadurl yang diubah; isi post lainnya tidak disentuh.
 */
class Migration_Post_download_links extends CI_Migration {

	public function up()
	{
		$this->load->library('wp_clone');
		$clone = $this->wp_clone;

		$posts = $this->db->select('id, content')->like('content', 'data-downloadurl="../')->get('posts')->result_array();
		foreach ($posts as $post)
		{
			$content = preg_replace_callback('/data-downloadurl="(\.\.\/[^"]*)"/', function ($m) use ($clone) {
				// Post berada satu tingkat di bawah root clone (<slug>/index.html).
				return 'data-downloadurl="'.$clone->map_url($m[1], 'post/', 'token').'"';
			}, $post['content']);

			if ($content !== $post['content'])
			{
				$this->db->where('id', $post['id'])->update('posts', array('content' => $content));
			}
		}
	}

	public function down()
	{
		// Tidak dikembalikan: path HTTrack lama memang tidak bisa dibuka di situs CI.
	}
}
