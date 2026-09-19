<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Impor paket Download Manager dari clone (download/<slug>/index.html) ke tabel downloads.
 *
 * Sumber file per paket (sesuai respons download yang tersimpan di clone):
 *  - isi file tersimpan HTTrack (index<hash>.html berisi PDF/ZIP): pakai PDF deskripsi jika identik,
 *    selain itu disalin ke wp-content/uploads/download-manager-files/<slug>.<ext>
 *  - redirect ke .../wp-content/uploads/<path>: pakai <path> jika file-nya ada di lokal, selain itu URL redirect
 *  - redirect ke situs lain (Google Drive): URL redirect
 *  - tidak ada respons di clone: diasumsikan = PDF di deskripsi (keputusan user; terbukti pada semua paket lain)
 */
class Wpdm_import {

	const FILES_DIR = 'download-manager-files/';

	protected $CI;
	protected $clone;
	protected $log = array();

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('wp_clone');
		$this->CI->load->helper('wp');
		$this->clone = $this->CI->wp_clone;
	}

	public function log()
	{
		return $this->log;
	}

	public function run()
	{
		$root = $this->clone->root();
		$rows = array();

		foreach (glob($root.'download/*/index.html') as $file)
		{
			$rel = substr($file, strlen($root));
			$rows[] = $this->package($rel);
		}

		$db = $this->CI->db;
		$db->trans_start();
		$db->query('DELETE FROM downloads');
		$db->insert_batch('downloads', $rows);
		$db->trans_complete();
		if ($db->trans_status() === FALSE)
		{
			throw new RuntimeException('Transaksi impor download gagal.');
		}

		return $rows;
	}

	protected function package($rel)
	{
		$dir = dirname($rel);
		$html = $this->clone->rewrite_urls($this->clone->clean($this->clone->read($rel)), $rel, 'token');

		$this->match('/<body class="[^"]*\bpostid-(\d+)\b/', $html, $id, $rel, 'ID');
		$this->match('/<h1 class="page-title" itemprop="headline">(.*?)<\/h1>/s', $html, $h1, $rel, 'judul');
		$this->match('/href="\{base_url\}author\/([^"]+)"/', $html, $author, $rel, 'author');
		$this->match('/<time class="ct-meta-element-date" datetime="([^"]+)"/', $html, $date, $rel, 'tanggal');
		$this->match("/<div class='w3eden' ><!-- WPDM Template: (Default Template(?: \\( Simplified \\))?) -->/", $html, $tpl, $rel, 'template');
		$this->match("/<a class='wpdm-download-link download-on-click btn btn-primary btn-sm' rel='nofollow' href='#' data-downloadurl=\"[^\"]*\">(.*?)<\\/a>/s", $html, $button, $rel, 'tombol');

		$badges = array();
		preg_match_all('/\n\s+(Download|File Size|Last Updated)\n\s+<span class="badge">([^<]*)<\/span>/', $html, $b, PREG_SET_ORDER);
		foreach ($b as $x)
		{
			$badges[$x[1]] = $x[2];
		}

		$template = ($tpl[1] === 'Default Template') ? 'default' : 'simplified';
		$open = ($template === 'default')
			? '#<div class="col-md-7">\n        <h1 class="mt-0">(.*?)</h1>\n        (.*?)\n\n        <div class="wel">#s'
			: '#<div class="col-md-7">\n\n        ()(.*?)\n\n        <div class="wel">#s';
		$this->match($open, $html, $desc, $rel, 'deskripsi');

		$author_row = $this->CI->db->where('slug', $author[1])->get('authors')->row_array();
		list($d, $m, $y) = explode('/', $badges['Last Updated']);

		return array(
			'id'                => (int) $id[1],
			'slug'              => substr($this->clone->real_path($dir), strlen('download/')),
			'title'             => $this->raw_title($h1[1], $desc[1], $html, $rel),
			'description'       => $desc[2],
			'template'          => $template,
			'button_label'      => ($button[1] === 'Download') ? NULL : html_entity_decode($button[1], ENT_QUOTES, 'UTF-8'),
			'file'              => $this->file_source($dir, $desc[2], (int) $id[1]),
			'file_size'         => $badges['File Size'],
			'download_count'    => (int) $badges['Download'],
			'author_id'         => (int) $author_row['id'],
			'featured_media_id' => $this->featured($html, $rel),
			'status'            => 'publish',
			'published_at'      => date('Y-m-d H:i:s', strtotime($date[1])),
			'modified_at'       => $y.'-'.$m.'-'.$d.' 00:00:00',
		);
	}

	/**
	 * Judul mentah. Template "Default" menampilkannya langsung; untuk template lain dibalik dari hasil
	 * wptexturize, lalu dipastikan wp_texturize() menghasilkan judul yang sama persis.
	 */
	protected function raw_title($rendered, $raw_from_template, $html, $rel)
	{
		if ($raw_from_template !== '')
		{
			$raw = html_entity_decode($raw_from_template, ENT_QUOTES, 'UTF-8');
		}
		else
		{
			$raw = preg_replace(array('/(?<=^| )&#8211;(?=$| )/', '/(?<=^| )&#8212;(?=$| )/'), array('-', '--'), $rendered);
			$raw = strtr($raw, array(
				'&#8211;' => '--', '&#8212;' => '---',
				'&#8220;' => '"', '&#8221;' => '"', '&#8216;' => "'", '&#8217;' => "'", '&#8230;' => '...',
				'&#038;' => '&', '&#215;' => 'x', '&#8242;' => "'", '&#8243;' => '"',
			));
			$raw = html_entity_decode($raw, ENT_QUOTES, 'UTF-8');
		}

		if (wp_texturize(str_replace(array('<', '>'), array('&lt;', '&gt;'), $raw)) !== $rendered && wp_texturize($raw) !== $rendered)
		{
			throw new RuntimeException('Judul mentah tidak cocok untuk '.$rel.': '.$rendered);
		}

		return $raw;
	}

	/**
	 * Path file (relatif wp-content/uploads/) atau URL luar.
	 */
	protected function file_source($dir, $description, $id)
	{
		$root = $this->clone->root();
		$desc_file = NULL;
		if (preg_match('/href="\{base_url\}wp-content\/uploads\/([^"]+)"/', $description, $m) && is_file(FCPATH.'wp-content/uploads/'.$m[1]))
		{
			$desc_file = $m[1];
		}

		foreach (glob($root.$dir.'/index*.html') as $variant)
		{
			if (basename($variant) === 'index.html')
			{
				continue;
			}
			$data = file_get_contents($variant);

			if (ltrim($data)[0] !== '<')
			{
				// Isi file tersimpan di clone.
				if ($desc_file && md5_file(FCPATH.'wp-content/uploads/'.$desc_file) === md5($data))
				{
					return $desc_file;
				}

				return $this->save_file(substr($dir, strlen('download/')), $data);
			}

			if (preg_match('/URL=([^"]+)"/', $data, $r))
			{
				if (preg_match('#^https?://[^/]+/wp-content/uploads/(.+)$#', $r[1], $u) && is_file(FCPATH.'wp-content/uploads/'.$u[1]))
				{
					return $u[1];
				}
				if (preg_match('#^https?://#', $r[1]))
				{
					return $r[1];
				}

				// Redirect ke file lokal clone = link rusak di situs asli (mis. "...2028.pd" yang dijawab 404,
				// disimpan HTTrack sebagai .pd.html). Pakai PDF deskripsi.
				if ($desc_file)
				{
					$this->log[] = 'paket '.$id.': redirect rusak di situs asli ('.$r[1].'), dipakai PDF deskripsi '.$desc_file;
					return $desc_file;
				}
				throw new RuntimeException('Redirect paket '.$id.' rusak dan tidak ada PDF deskripsi.');
			}
		}

		if ( ! $desc_file)
		{
			throw new RuntimeException('Sumber file paket '.$id.' ('.$dir.') tidak ditemukan.');
		}
		$this->log[] = 'paket '.$id.': file diasumsikan = PDF deskripsi ('.$desc_file.')';

		return $desc_file;
	}

	protected function save_file($slug, $data)
	{
		$ext = 'bin';
		if (strpos($data, '%PDF') === 0)
		{
			$ext = 'pdf';
		}
		elseif (strpos($data, "PK\x03\x04") === 0)
		{
			$ext = (strpos($data, 'ppt/') !== FALSE) ? 'pptx' : ((strpos($data, 'word/') !== FALSE) ? 'docx' : ((strpos($data, 'xl/') !== FALSE) ? 'xlsx' : 'zip'));
		}

		$path = self::FILES_DIR.$slug.'.'.$ext;
		$target = FCPATH.'wp-content/uploads/'.$path;
		if ( ! is_dir(dirname($target)))
		{
			mkdir(dirname($target), 0775, TRUE);
		}
		if ( ! is_file($target))
		{
			file_put_contents($target, $data);
			$this->log[] = 'salin file dari clone: wp-content/uploads/'.$path;
		}
		elseif (md5_file($target) !== md5($data))
		{
			throw new RuntimeException('File berbeda dengan nama sama sudah ada: '.$path);
		}

		return $path;
	}

	/**
	 * Gambar unggulan paket (card [featured_image]) dicocokkan dengan tabel media lewat nama file.
	 */
	protected function featured($html, $rel)
	{
		if ( ! preg_match('/<div class="card mb-3 p-3 hide_empty \[hide_empty:featured_image\]"><img [^>]*src="\{base_url\}wp-content\/uploads\/([^"]+)"/', $html, $m))
		{
			return NULL;
		}
		$media = $this->CI->db->where('file', $m[1])->get('media')->row_array();
		if ( ! $media)
		{
			throw new RuntimeException('Gambar unggulan '.$m[1].' ('.$rel.') tidak ada di tabel media.');
		}

		return (int) $media['id'];
	}

	protected function match($pattern, $html, &$m, $rel, $what)
	{
		if ( ! preg_match($pattern, $html, $m))
		{
			throw new RuntimeException('Tidak menemukan '.$what.' di '.$rel);
		}
	}
}
