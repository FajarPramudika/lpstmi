<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pembatas laju sederhana berbasis file (audit keamanan 2026-09-20, temuan 12).
 *
 * Dipakai untuk endpoint publik yang mahal (pencarian: satu request ~6x biaya halaman biasa karena
 * LIKE '%kata%' atas UNION posts+pages+downloads, dan kolom kontennya berisi HTML puluhan KB).
 *
 * Sengaja tidak memakai database: pembatas yang menulis satu baris per request justru menambah beban
 * yang mau dikurangi. Login memakai tabel karena di sana volumenya kecil dan catatannya perlu tahan lama.
 *
 * Batasnya per IP, jadi ini hanya menahan penyerang dari satu host; serangan terdistribusi tetap lolos.
 * Di belakang proxy/CDN, isi $config['proxy_ips'] atau semua pengunjung akan dihitung sebagai satu IP.
 */
class Rate_limit {

	/** Peluang penyapuan berkas kedaluwarsa per request (1 dari sekian). */
	const GC_CHANCE = 200;

	protected $dir;
	protected $retry_after = 0;

	public function __construct()
	{
		$this->dir = APPPATH.'cache/ratelimit/';
	}

	/**
	 * Catat satu request pada $bucket. Mengembalikan TRUE kalau masih dalam batas.
	 *
	 * @param string $bucket  pengenal, mis. 'search:1.2.3.4'
	 * @param int    $limit   jumlah request yang diizinkan dalam satu jendela
	 * @param int    $seconds panjang jendela
	 */
	public function hit($bucket, $limit, $seconds)
	{
		if ( ! is_dir($this->dir) && ! @mkdir($this->dir, 0775, TRUE))
		{
			// Folder tidak bisa dibuat: jangan sampai pembatas ini malah membuat situs tidak bisa diakses.
			return TRUE;
		}

		if (mt_rand(1, self::GC_CHANCE) === 1)
		{
			$this->gc($seconds);
		}

		$file = $this->dir.md5($bucket).'.txt';
		$now = time();
		$count = 0;
		$reset = $now + $seconds;

		$raw = @file_get_contents($file);
		if ($raw !== FALSE && strpos($raw, '|') !== FALSE)
		{
			list($stored_count, $stored_reset) = explode('|', $raw, 2);
			if ((int) $stored_reset > $now)
			{
				$count = (int) $stored_count;
				$reset = (int) $stored_reset;
			}
		}

		$count++;
		@file_put_contents($file, $count.'|'.$reset, LOCK_EX);
		$this->retry_after = max(1, $reset - $now);

		return $count <= $limit;
	}

	/** Detik sampai jendela berikutnya, untuk header Retry-After. */
	public function retry_after()
	{
		return $this->retry_after;
	}

	/** Buang berkas yang jendelanya sudah lewat, supaya folder tidak menumpuk. */
	protected function gc($seconds)
	{
		$cutoff = time() - max(60, $seconds) * 2;
		foreach ((array) glob($this->dir.'*.txt') as $file)
		{
			if (@filemtime($file) < $cutoff)
			{
				@unlink($file);
			}
		}
	}
}
