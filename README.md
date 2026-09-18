# Migrasi stmi.ac.id ke CodeIgniter 3

Proyek ini adalah hasil migrasi website Politeknik STMI Jakarta dari WordPress (tema Blocksy + Elementor) menjadi aplikasi **CodeIgniter 3.1.13**. Tujuan utama proyek ini adalah mempertahankan tampilan situs agar **sama persis** (byte-identik) dengan versi WordPress aslinya, sambil memindahkan manajemen konten ke dalam CodeIgniter.

## Persyaratan Sistem

- **PHP 7.3** (Wajib, jangan gunakan PHP 7.4 ke atas karena menggunakan fitur lama CI3 dan ekstensi terkait)
- **Database MariaDB 10.4** (Bawaan XAMPP)
- Web Server untuk production (Apache/Nginx) atau built-in PHP server untuk development.

## Instalasi & Menjalankan Lokal

1. Pastikan ekstensi database dan module PHP yang dibutuhkan sudah aktif.
2. Buat database bernama `lpstmi_db`.
3. Jalankan migrasi database awal (jika belum ada tabel):
   ```bash
   php index.php tools migrate
   ```
4. Untuk menjalankan server development lokal, gunakan perintah berikut di terminal:
   ```bash
   php -d upload_max_filesize=20M -d post_max_size=25M -S localhost:8000 server.php
   ```
   *Catatan: Jangan gunakan Apache bawaan XAMPP jika versi PHP-nya 8.x.*
5. Buka situs di `http://localhost:8000/`.

## Struktur Direktori

- `application/` - Kode inti CodeIgniter 3 (Controllers, Models, Views, Config).
- `system/` - Core system CodeIgniter 3.
- `assets/admin/` - CSS dan JS untuk panel admin kustom.
- `wp-content/` & `wp-includes/` - Direktori aset statis bawaan hasil clone WordPress (dipertahankan agar URL aset tidak rusak).
- `stmi.ac.id-clone/` - Direktori read-only berisi hasil clone statis dari HTTrack sebagai referensi.
- `server.php` - Router khusus untuk menjalankan aplikasi lewat PHP Built-in Web Server.

## Admin Panel

Panel administrasi dibangun kustom (bukan bawaan WordPress) dan bisa diakses lewat:
- **URL:** `http://localhost:8000/admin`
- Anda dapat mengelola Post, Kategori, Tag, Media, dan Pengguna melalui panel ini.
- Untuk membuat user pertama kali melalui terminal:
  ```bash
  php index.php tools set_login <slug-author> <username> admin
  ```
  *(Password akan di-generate dan ditampilkan di layar)*

## Perintah CLI (Tools)

Aplikasi ini dilengkapi berbagai tool command-line (CLI) untuk manajemen data dan konversi halaman statis:

| Perintah | Deskripsi |
| --- | --- |
| `php index.php tools migrate` | Menjalankan migrasi struktur database. |
| `php index.php tools import_posts` | Mengimpor data post, media, kategori dari hasil clone statis. |
| `php index.php tools check` | Mengecek kecocokan halaman statis clone terhadap layout master. |
| `php index.php tools convert <slug>` | Mengonversi file HTML halaman statis clone menjadi View CI. |
| `php index.php tools verify <slug>` | Memverifikasi tampilan halaman statis yang dirender CI agar byte-identik dengan clone aslinya. |
| `php index.php tools verify_db` | Memverifikasi seluruh halaman dinamis (Post & Arsip) agar byte-identik. |

## Konvensi dan Aturan Penting
- **Jangan memodifikasi file di dalam folder `stmi.ac.id-clone/`** karena berfungsi sebagai acuan validasi tampilan.
- Sebagian besar halaman statis dikonversi secara atomik agar markup HTML, kelas CSS Elementor, dan tag `<style>` inline bawaan WP tidak bergeser atau pecah ukurannya.
- Semua URL hardcode ke `stmi.ac.id` dikonversi ke URL relatif lokal lewat fungsi `base_url()` dan `site_url()`.
