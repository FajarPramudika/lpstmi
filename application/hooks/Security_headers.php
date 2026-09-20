<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Header keamanan untuk semua respons yang lewat CodeIgniter (audit keamanan 2026-09-20, temuan 10).
 *
 * Sengaja di PHP, bukan hanya di .htaccess, supaya ikut berlaku di server dev (php -S) dan di nginx
 * yang mengabaikan .htaccess. .htaccess tetap memasang header yang sama untuk file statis di Apache.
 *
 * Yang SENGAJA TIDAK dipasang:
 * - `object-src 'none'`: halaman /statistik memakai <object class='tableauViz'> untuk embed Tableau.
 * - `script-src`: halaman hasil migrasi penuh dengan <script> inline milik Elementor/Blocksy, jadi
 *   kebijakan yang berguna baru mungkin setelah inline script diberi nonce.
 * - `includeSubDomains` pada HSTS: subdomain (jarvis, lib, ppid, e-learning, dst.) belum tentu HTTPS,
 *   dan flag itu akan membuatnya tidak bisa dibuka sama sekali. Jangan ditambahkan sebelum semuanya HTTPS.
 */
class Security_headers {

	public function send()
	{
		if (is_cli() OR headers_sent())
		{
			return;
		}

		// Browser tidak boleh menebak tipe konten (penting untuk file yang diunggah pengguna).
		header('X-Content-Type-Options: nosniff');

		// Jangan bocorkan URL lengkap (termasuk URL admin) ke situs lain lewat Referer.
		header('Referrer-Policy: strict-origin-when-cross-origin');

		// Anti-clickjacking: frame-ancestors untuk browser modern, X-Frame-Options untuk yang lama.
		header('X-Frame-Options: SAMEORIGIN');
		header("Content-Security-Policy: frame-ancestors 'self'; base-uri 'self'");

		header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

		if (ENVIRONMENT === 'production' && ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		{
			header('Strict-Transport-Security: max-age=31536000');
		}
	}
}
