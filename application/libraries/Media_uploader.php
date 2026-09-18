<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Upload file ke wp-content/uploads/YYYY/MM/ dan buat ukuran gambar seperti WordPress.
 */
class Media_uploader {

	/** Tipe file yang diizinkan: ekstensi => MIME. */
	public static $types = array(
		'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp',
		'pdf'  => 'application/pdf',
		'doc'  => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'xls'  => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'ppt'  => 'application/vnd.ms-powerpoint', 'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'zip'  => 'application/zip',
	);

	/** Batas ukuran file (byte). */
	const MAX_SIZE = 20971520;

	/** Gambar lebih besar dari ini diperkecil menjadi versi -scaled (big_image_size_threshold WordPress). */
	const BIG_IMAGE = 2560;

	/**
	 * Ukuran turunan WordPress, urut sesuai prioritas pembuatan (urutan di metadata sejak WP 5.3).
	 * [lebar maks, tinggi maks, crop]
	 */
	protected $sizes = array(
		'medium'       => array(300, 300, FALSE),
		'large'        => array(1024, 1024, FALSE),
		'thumbnail'    => array(150, 150, TRUE),
		'medium_large' => array(768, 0, FALSE),
		'1536x1536'    => array(1536, 1536, FALSE),
		'2048x2048'    => array(2048, 2048, FALSE),
	);

	protected $error = '';

	public function error()
	{
		return $this->error;
	}

	/**
	 * Proses satu file dari $_FILES. Mengembalikan data baris tabel media, atau FALSE.
	 */
	public function handle(array $file)
	{
		if ( ! isset($file['error']) OR $file['error'] !== UPLOAD_ERR_OK)
		{
			$this->error = $this->upload_error(isset($file['error']) ? $file['error'] : UPLOAD_ERR_NO_FILE);
			return FALSE;
		}
		if ($file['size'] > self::MAX_SIZE)
		{
			$this->error = 'Ukuran file melebihi 20 MB.';
			return FALSE;
		}

		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if ( ! isset(self::$types[$ext]))
		{
			$this->error = 'Tipe file .'.$ext.' tidak diizinkan.';
			return FALSE;
		}

		$mime = self::$types[$ext];
		$is_image = (strpos($mime, 'image/') === 0);
		if ($is_image)
		{
			$info = @getimagesize($file['tmp_name']);
			if ($info === FALSE OR image_type_to_mime_type($info[2]) !== $mime && ! ($mime === 'image/jpeg' && $info[2] === IMAGETYPE_JPEG))
			{
				$this->error = 'File bukan gambar yang valid.';
				return FALSE;
			}
		}

		$subdir = date('Y/m');
		$dir = FCPATH.'wp-content/uploads/'.$subdir.'/';
		if ( ! is_dir($dir) && ! mkdir($dir, 0775, TRUE))
		{
			$this->error = 'Folder upload tidak bisa dibuat.';
			return FALSE;
		}

		$name = $this->unique_name($dir, $this->sanitize_name(pathinfo($file['name'], PATHINFO_FILENAME)), $ext);
		if ( ! move_uploaded_file($file['tmp_name'], $dir.$name.'.'.$ext))
		{
			$this->error = 'File gagal disimpan.';
			return FALSE;
		}

		$row = array(
			'file'       => $subdir.'/'.$name.'.'.$ext,
			'width'      => 0,
			'height'     => 0,
			'alt'        => '',
			'mime_type'  => $mime,
			'sizes'      => '{}',
			'created_at' => date('Y-m-d H:i:s'),
		);

		if ($is_image)
		{
			$row = array_merge($row, $this->make_sizes($subdir, $name, $ext, $info[0], $info[1]));
		}

		return $row;
	}

	/**
	 * Buat ukuran turunan. Nama file: <nama>-<lebar>x<tinggi>.<ext>.
	 */
	protected function make_sizes($subdir, $name, $ext, $width, $height)
	{
		$dir = FCPATH.'wp-content/uploads/'.$subdir.'/';
		$source = $dir.$name.'.'.$ext;
		$image = $this->load($source, $ext);
		if ( ! $image)
		{
			return array('width' => $width, 'height' => $height);
		}

		$file = $name.'.'.$ext;
		$full_w = $width;
		$full_h = $height;

		// Gambar sangat besar: file utama menjadi <nama>-scaled.<ext>, ukuran turunan dibuat dari aslinya.
		if ($width > self::BIG_IMAGE OR $height > self::BIG_IMAGE)
		{
			list($full_w, $full_h) = wp_constrain_dimensions($width, $height, self::BIG_IMAGE, self::BIG_IMAGE);
			$file = $name.'-scaled.'.$ext;
			$this->save($this->resize($image, $width, $height, $full_w, $full_h, 0, 0, $width, $height), $dir.$file, $ext);
		}

		$sizes = array();
		foreach ($this->sizes as $size => $spec)
		{
			list($max_w, $max_h, $crop) = $spec;
			$dims = $this->dimensions($width, $height, $max_w, $max_h, $crop);
			if ( ! $dims)
			{
				continue;
			}
			list($dst_w, $dst_h, $src_x, $src_y, $src_w, $src_h) = $dims;
			$size_file = $name.'-'.$dst_w.'x'.$dst_h.'.'.$ext;
			$this->save($this->resize($image, $width, $height, $dst_w, $dst_h, $src_x, $src_y, $src_w, $src_h), $dir.$size_file, $ext);
			$sizes[$size] = array('file' => $size_file, 'width' => $dst_w, 'height' => $dst_h);
		}
		imagedestroy($image);

		return array(
			'file'   => $subdir.'/'.$file,
			'width'  => $full_w,
			'height' => $full_h,
			'sizes'  => json_encode($sizes),
		);
	}

	/**
	 * Dimensi hasil seperti image_resize_dimensions() WordPress; NULL jika tidak perlu dibuat.
	 */
	protected function dimensions($orig_w, $orig_h, $dest_w, $dest_h, $crop)
	{
		if ($crop)
		{
			$new_w = min($dest_w, $orig_w);
			$new_h = min($dest_h, $orig_h);
			$ratio = max($new_w / $orig_w, $new_h / $orig_h);
			$crop_w = (int) round($new_w / $ratio);
			$crop_h = (int) round($new_h / $ratio);
			$s_x = (int) floor(($orig_w - $crop_w) / 2);
			$s_y = (int) floor(($orig_h - $crop_h) / 2);
		}
		else
		{
			$crop_w = $orig_w;
			$crop_h = $orig_h;
			$s_x = $s_y = 0;
			list($new_w, $new_h) = wp_constrain_dimensions($orig_w, $orig_h, $dest_w, $dest_h);
		}

		if ($new_w >= $orig_w && $new_h >= $orig_h)
		{
			return NULL;
		}

		return array((int) $new_w, (int) $new_h, $s_x, $s_y, $crop_w, $crop_h);
	}

	protected function load($path, $ext)
	{
		switch ($ext)
		{
			case 'png':  return @imagecreatefrompng($path);
			case 'gif':  return @imagecreatefromgif($path);
			case 'webp': return function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : FALSE;
			default:     return @imagecreatefromjpeg($path);
		}
	}

	protected function resize($image, $orig_w, $orig_h, $dst_w, $dst_h, $src_x, $src_y, $src_w, $src_h)
	{
		$new = imagecreatetruecolor($dst_w, $dst_h);
		imagealphablending($new, FALSE);
		imagesavealpha($new, TRUE);
		imagefill($new, 0, 0, imagecolorallocatealpha($new, 0, 0, 0, 127));
		imagecopyresampled($new, $image, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

		return $new;
	}

	protected function save($image, $path, $ext)
	{
		switch ($ext)
		{
			case 'png':  imagepng($image, $path, 9); break;
			case 'gif':  imagegif($image, $path); break;
			case 'webp': imagewebp($image, $path, 82); break;
			default:     imagejpeg($image, $path, 82);
		}
		imagedestroy($image);
	}

	protected function sanitize_name($name)
	{
		// Seperti sanitize_file_name() WordPress: huruf besar/kecil dipertahankan.
		$converted = function_exists('iconv') ? @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) : FALSE;
		$name = ($converted !== FALSE) ? $converted : $name;
		$name = trim(preg_replace('/[^A-Za-z0-9_.-]+/', '-', $name), '.-_');

		return ($name === '') ? 'file' : substr($name, 0, 120);
	}

	protected function unique_name($dir, $name, $ext)
	{
		$candidate = $name;
		for ($i = 1; file_exists($dir.$candidate.'.'.$ext) OR file_exists($dir.$candidate.'-scaled.'.$ext); $i++)
		{
			$candidate = $name.'-'.$i;
		}

		return $candidate;
	}

	protected function upload_error($code)
	{
		switch ($code)
		{
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE: return 'Ukuran file melebihi batas server (upload_max_filesize).';
			case UPLOAD_ERR_PARTIAL:   return 'File hanya terunggah sebagian.';
			case UPLOAD_ERR_NO_FILE:   return 'Tidak ada file yang dipilih.';
			default:                   return 'Upload gagal (kode '.$code.').';
		}
	}
}
