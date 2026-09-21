# Migrasi stmi.ac.id ke CodeIgniter 3

Proyek ini adalah hasil migrasi website Politeknik STMI Jakarta dari WordPress (tema Blocksy + Elementor) menjadi aplikasi **CodeIgniter 3.1.13**. Tujuan utama proyek ini adalah mempertahankan tampilan situs agar **sama persis** (byte-identik) dengan versi WordPress aslinya, sambil memindahkan manajemen konten ke dalam CodeIgniter.

Dokumen ini fokus pada **konfigurasi yang dibutuhkan untuk menjalankan aplikasi**, di sisi development maupun production. Aturan kerja dan detail teknis migrasi ada di `CLAUDE.md`.

## Persyaratan Sistem

- **PHP 7.3** (wajib). Jangan pakai PHP 7.4 ke atas: kode ini memakai gaya CI3 lama dan belum diuji di versi baru.
- **MariaDB 10.4** / MySQL 5.7+, database `utf8mb4`.
- Ekstensi PHP: `mysqli`, `gd`, `fileinfo`, `mbstring`, `iconv`, `json`.
  - `gd` dipakai membuat ukuran turunan gambar ala WordPress, `fileinfo` untuk memvalidasi isi file yang diunggah.
- Batas upload PHP: `upload_max_filesize` ≥ **20M** dan `post_max_size` ≥ **25M** (batas aplikasi 20 MB per berkas).
- Web server: Apache dengan **`mod_rewrite`** dan **`mod_headers`**, atau nginx (lihat catatan di bagian production).

## Konfigurasi

Tidak ada kredensial yang ditulis di dalam kode. Semua nilai yang berbeda antar-mesin dibaca dari dua sumber:

1. `application/config/database.local.php` — **hanya untuk development**, tidak ikut git.
2. **Variabel environment** — dipakai di production, dan **menimpa** file lokal di atas kalau keduanya ada.

Urutan itu disengaja: server production tidak bisa salah memakai kredensial development walaupun file lokalnya ikut ter-copy.

| Variabel | Wajib | Keterangan |
| --- | --- | --- |
| `DB_HOST` | ya (production) | Host database. Di lokal **harus `127.0.0.1`**, bukan `localhost` — PHP 7.3 sistem tidak menemukan socket XAMPP. |
| `DB_USER` | ya (production) | User database aplikasi. **Jangan `root`.** |
| `DB_PASS` | ya (production) | Password user database. |
| `DB_NAME` | ya (production) | Nama database, mis. `lpstmi_db`. |
| `CI_BASE_URL` | ya (production) | URL situs, **harus diakhiri garis miring**, mis. `https://stmi.ac.id/`. Default `http://localhost:8000/`. |
| `CI_ENV` | tidak | `development` / `production`. Lihat tabel di bawah — biasanya tidak perlu diisi. |
| `CI_ENCRYPTION_KEY` | tidak | Belum dipakai kode mana pun (session memakai driver `files`). Isi kalau nanti library `Encryption` dipakai. |

### Environment

`ENVIRONMENT` menentukan apakah pesan error tampil ke pengunjung dan apakah cookie diberi flag `Secure`. Defaultnya sengaja aman, jadi **lupa menyetel `CI_ENV` tidak akan membocorkan apa pun**:

| Dijalankan lewat | `ENVIRONMENT` | Efek |
| --- | --- | --- |
| Web (Apache/nginx → `index.php`) | `production` | Error disembunyikan, `cookie_secure = TRUE` (butuh HTTPS). |
| `server.php` (php -S, development) | `development` | Error tampil lengkap, `cookie_secure = FALSE`. Disetel sendiri oleh `server.php`. |
| CLI (`php index.php tools …`) | `development` | Error tampil di terminal. |

Untuk memaksa: `CI_ENV=development php index.php tools verify home`.

### Folder yang harus bisa ditulis

- `application/cache/` — berisi `sessions/` (session admin) dan `ratelimit/` (pembatas laju); keduanya dibuat otomatis.
- `application/logs/` — satu berkas log per hari.
- `wp-content/uploads/` — berkas yang diunggah lewat panel admin.

---

## Menjalankan di Development

### 1. Siapkan database dan user-nya

```sql
CREATE DATABASE lpstmi_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE USER 'lpstmi_app'@'127.0.0.1' IDENTIFIED BY '<password-acak>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP, REFERENCES
  ON lpstmi_db.* TO 'lpstmi_app'@'127.0.0.1';
FLUSH PRIVILEGES;
```

Hak `CREATE/ALTER/INDEX/DROP` dibutuhkan untuk menjalankan migrasi. Jangan memberi `ALL PRIVILEGES` atau hak ke `*.*`.

### 2. Isi kredensial lokal

```bash
cp application/config/database.local.php.example application/config/database.local.php
```

Lalu isi `username` dan `password` sesuai user yang barusan dibuat. Berkas ini **tidak ikut git** — jangan pernah menulis password di `application/config/database.php`.

### 3. Jalankan migrasi

```bash
php index.php tools migrate
```

### 4. Muat konten dari seed

```bash
php index.php tools import_seed
```

Seed (`application/seeds/seed.sql`, ikut git) berisi seluruh konten situs: post, halaman statis, paket download, media, menu, beranda, footer, dan kontak — **termasuk hasil editan lewat admin**. Kolom login di `authors` (username, email, password) sengaja kosong, jadi akun admin dibuat di langkah berikutnya.

> **Jangan memakai `import_posts`/`import_pages` untuk menyiapkan mesin baru.** Perintah itu membaca hasil clone, dan **32 halaman statis tidak bisa dibangun ulang dari clone** (view sumbernya sudah dihapus setelah dipindah ke database). Hasilnya situs tanpa error, tetapi `/sejarah-kampus` dan halaman statis lain 404. Perintah `import_*` hanya berguna untuk membangun ulang post/download dari clone, dan `import_posts` **mengosongkan tabel konten lebih dulu**.

`import_seed` menolak jalan kalau database sudah berisi konten. `import_seed ulang` menimpanya (post, halaman, dan editan admin di database itu hilang; akun login dipertahankan). Seed harus dimuat setelah `migrate`, dengan versi skema yang sama.

**Membagikan konten terbaru** (dari mesin yang kontennya dikelola):

```bash
php index.php tools export_seed    # tulis ulang application/seeds/seed.sql, lalu commit
```

Seed hanya berisi baris database. Berkas yang diunggah lewat admin ada di `wp-content/uploads/` dan harus ikut di-commit juga, atau gambarnya akan hilang di mesin lain.

### 5. Buat akun admin pertama

```bash
php index.php tools set_login <slug-author> <username> admin
```

Password acak ditampilkan sekali di layar. Ganti lewat **Profil saya** setelah login. Agar bisa login memakai email, isi kolom Email pengguna di `/admin/users` (email harus unik).

### 6. Jalankan server

```bash
php -d upload_max_filesize=20M -d post_max_size=25M -S localhost:8000 server.php
```

Situs: `http://localhost:8000/` · Panel admin: `http://localhost:8000/admin`

> Jangan pakai Apache bawaan XAMPP kalau PHP-nya 8.x. `server.php` adalah router khusus built-in server: berkas statis dilayani langsung, sisanya diteruskan ke `index.php`.

---

## Menjalankan di Production

### 0. Berkas yang disalin ke server

Situs **tidak membutuhkan** `stmi.ac.id-clone/` (±500 MB): folder itu hanya dipakai perintah CLI untuk konversi, impor, dan verifikasi tampilan. Konten di server berasal dari database (seed), bukan dari clone. Folder `.git/` juga tidak perlu ada di docroot. Cara termudah menyalin hanya yang dibutuhkan:

```bash
git archive --format=tar HEAD | tar -x -C /var/www/lpstmi --exclude='stmi.ac.id-clone'
```

Hasilnya tanpa `stmi.ac.id-clone/` dan tanpa `.git/`. `git archive` mengambil isi **commit terakhir**, jadi commit dulu perubahan yang ingin ikut.

Kalau server tetap di-deploy dengan `git clone`/`git pull` langsung ke docroot, `.htaccess` sudah memblokir `.git` dan `stmi.ac.id-clone/` (403) sebagai jaring pengaman — tetapi di nginx aturan itu harus ditulis ulang (lihat langkah 5).

### 1. Variabel environment di vhost

Letakkan di konfigurasi vhost Apache, **di luar docroot**. Jangan menaruhnya di `.htaccess`, karena `.htaccess` bisa terbaca kalau konfigurasi server salah.

```apache
SetEnv DB_HOST 127.0.0.1
SetEnv DB_USER lpstmi_app
SetEnv DB_PASS <password produksi>
SetEnv DB_NAME lpstmi_db
SetEnv CI_BASE_URL https://stmi.ac.id/
```

Jangan menyalin `application/config/database.local.php` ke server production — cukup environment di atas.

**Perintah CLI tidak membaca `SetEnv`.** Variabel di vhost hanya berlaku untuk request yang lewat Apache; `php index.php tools …` di terminal tidak melihatnya, dan karena `database.local.php` memang tidak ada di server, username database-nya kosong sehingga perintah gagal. Simpan nilai yang sama di file env **di luar docroot**, lalu muat sebelum menjalankan CLI:

```bash
# /etc/lpstmi.env — pemilik root, chmod 600
DB_HOST=127.0.0.1
DB_USER=lpstmi_app
DB_PASS=<password produksi>
DB_NAME=lpstmi_db
CI_BASE_URL=https://stmi.ac.id/
```

```bash
set -a; . /etc/lpstmi.env; set +a
php index.php tools migrate
```

Setiap kali mengganti password database, ubah di **kedua** tempat (vhost dan file env).

### 2. HTTPS wajib

Di production `cookie_secure` otomatis menjadi `TRUE`, sehingga cookie sesi hanya dikirim lewat HTTPS. **Tanpa HTTPS penuh, login tidak akan bisa.** Siapkan sertifikat dan redirect HTTP → HTTPS, lalu aktifkan baris HSTS yang sudah disiapkan (dalam keadaan dikomentari) di `.htaccess`:

```apache
Header always set Strict-Transport-Security "max-age=31536000"
```

> Jangan menambahkan `includeSubDomains` sebelum **semua** subdomain (`jarvis`, `lib`, `ppid`, `e-learning`, …) juga HTTPS — flag itu akan membuat subdomain yang masih HTTP tidak bisa dibuka sama sekali.

### 3. Database

Buat user dengan hak terbatas seperti pada langkah development, lalu jalankan migrasi di server. Untuk peluncuran pertama (database kosong), muat seed dan buat akun admin:

```bash
set -a; . /etc/lpstmi.env; set +a                # kredensial DB untuk CLI (lihat langkah 1)
php index.php tools migrate
php index.php tools import_seed                  # hanya sekali, saat database masih kosong
php index.php tools set_login <slug-author> <username> admin
```

Setelah situs berjalan, **database produksi menjadi sumber konten**. Jangan memuat seed lagi ke sana (`import_seed ulang` menimpa semua perubahan dari admin). Cadangan harus mencakup **dua hal**:

- database: `mysqldump` biasa;
- berkas unggahan: folder `wp-content/uploads/` — `mysqldump` hanya menyimpan baris tabel `media`, bukan berkas gambar/PDF-nya. Tanpa folder ini, pemulihan dari cadangan menghasilkan situs dengan gambar dan unduhan yang hilang.

### 4. Di belakang proxy / CDN

Kalau situs berada di belakang Cloudflare, load balancer, atau reverse proxy, isi `proxy_ips` di `application/config/config.php`:

```php
$config['proxy_ips'] = '10.0.0.1, 10.0.0.2';
```

**Ini penting.** Tanpa itu semua pengunjung terlihat berasal dari satu IP, sehingga pembatas percobaan login dan pembatas laju pencarian akan mengunci semua orang sekaligus.

### 5. Kalau memakai nginx

nginx **mengabaikan `.htaccess`**, jadi semua aturan berikut harus ditulis ulang di konfigurasi server:

```nginx
# Front controller
location / { try_files $uri $uri/ /index.php$is_args$args; }

# Folder yang tidak boleh diakses publik
location ^~ /application/ { deny all; }   # berisi session aktif & log
location ^~ /system/      { deny all; }
location ^~ /stmi.ac.id-clone/ { deny all; }
location ~ /\.git { deny all; }                # .git/, .gitignore: riwayat berisi source & kredensial lama

# Jangan pernah mengeksekusi skrip di folder unggahan
location ~* ^/wp-content/uploads/.*\.(php|phtml|phar|cgi|pl|py|sh)$ { deny all; }

# Header keamanan untuk berkas statis (respons PHP sudah dapat dari aplikasi)
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header Content-Security-Policy "frame-ancestors 'self'; base-uri 'self'" always;
```

> Awas perilaku nginx: begitu sebuah `location` memakai `add_header`-nya sendiri, **semua** `add_header` dari
> level di atasnya berhenti berlaku di blok itu. Jadi header di atas harus diulang di setiap `location` yang
> punya `add_header` sendiri. Setelah deploy, pastikan dengan
> `curl -sI https://stmi.ac.id/wp-content/uploads/<berkas> | grep -i nosniff`.

### 6. Pemeliharaan

- **Log** menumpuk satu berkas per hari di `application/logs/`. Belum ada pembersihan otomatis; siapkan cron penghapus berkas lama bila perlu.
- **Peristiwa keamanan** (login berhasil/gagal, perubahan akun dan peran) dicatat di log yang sama dengan awalan `[keamanan]`.

---

## Struktur Direktori

- `application/` — Kode inti CodeIgniter 3 (Controllers, Models, Views, Config).
- `system/` — Core system CodeIgniter 3, **jangan diubah**.
- `assets/admin/` — CSS dan JS untuk panel admin kustom.
- `wp-content/` & `wp-includes/` — Aset statis hasil clone WordPress (dipertahankan agar URL aset tidak rusak). Unggahan dari admin juga masuk ke `wp-content/uploads/`.
- `stmi.ac.id-clone/` — Direktori **read-only** berisi hasil clone HTTrack sebagai acuan verifikasi tampilan.
- `server.php` — Router khusus untuk PHP built-in web server (development).

## Panel Admin

Panel administrasi dibangun kustom (bukan bawaan WordPress), diakses di `/admin`.

- **Login** memakai **username atau email** + password.
- **Peran admin**: semua fitur, termasuk pengguna, menu, beranda, link footer, dan kontak.
- **Peran editor**: dasbor, post, halaman, download, media, kategori, tag, dan profil sendiri.
- Mengganti password sendiri wajib mengisi password saat ini, dan akan mengakhiri sesi di perangkat lain.

## Perintah CLI (Tools)

Semua perintah hanya bisa dijalankan dari terminal.

| Perintah | Deskripsi |
| --- | --- |
| `php index.php tools migrate` | Menjalankan migrasi struktur database. |
| `php index.php tools set_login <slug> <username> [admin\|editor]` | Memberi akses login panel admin ke seorang author. |
| `php index.php tools import_seed [ulang]` | Memuat seluruh konten dari `application/seeds/seed.sql`. Cara menyiapkan mesin baru. |
| `php index.php tools export_seed` | Menulis konten database ke `application/seeds/seed.sql` (tanpa kredensial). |
| `php index.php tools import_posts` | Mengimpor post, media, kategori dari hasil clone. **Mengosongkan tabel konten dulu.** |
| `php index.php tools import_downloads [ulang]` | Mengimpor paket Download Manager. |
| `php index.php tools import_pages` | Memindahkan halaman statis ke tabel `pages` (aman diulang). |
| `php index.php tools import_media` | Mendaftarkan berkas `wp-content/uploads/` ke pustaka media (aman diulang). |
| `php index.php tools download_shortcodes` | Mengganti salinan kartu download di konten dengan kode pendek (aman diulang). |
| `php index.php tools check [folder]` | Mengecek kecocokan halaman clone terhadap layout bersama. |
| `php index.php tools convert <slug> [path]` | Mengonversi halaman statis clone menjadi View CI. |
| `php index.php tools verify [slug\|all] [dump]` | Memverifikasi render CI byte-identik dengan clone. `dump` menyimpan keduanya untuk di-diff. |
| `php index.php tools verify_db [tipe] [detail]` | Memverifikasi seluruh konten dari database (post, arsip, paket, halaman). |

**Server harus berjalan** saat menjalankan `verify`, karena perintah itu mengambil halaman lewat HTTP dari `base_url`.

### Uji regresi tampilan

Setelah mengubah template, helper, atau view, jalankan:

```bash
php index.php tools verify_db     # harus: OK 501, BEDA 0
php index.php tools check         # harus: 502 OK
php index.php tools verify all
```

## Konvensi dan Aturan Penting

- **Jangan memodifikasi apa pun di `stmi.ac.id-clone/`** — folder itu acuan validasi tampilan.
- **Jangan menulis kredensial di `application/config/database.php`** (ikut git). Pakai `database.local.php` atau environment.
- Markup halaman disalin apa adanya; kelas CSS Elementor, `<style>` inline, dan whitespace tidak boleh bergeser.
- URL ke `stmi.ac.id` dikonversi lewat `base_url()` / `site_url()`, dan disimpan di database sebagai token `{base_url}`.
- Controller admin baru **wajib** menyatakan `protected $roles` sendiri kalau editor boleh mengaksesnya; defaultnya admin saja.
