# Migrasi stmi.ac.id: WordPress (clone HTTrack) ke CodeIgniter 3

Tujuan: memindahkan website Politeknik STMI Jakarta (WordPress + tema Blocksy + Elementor) ke **CodeIgniter 3.1.13**
dengan tampilan yang **sama persis** dengan aslinya. Tampilan tidak boleh berubah sedikit pun.

Balas user dalam **Bahasa Indonesia**.

## Stack (wajib)

- **PHP 7.3** (`php -v` → 7.3.33). Jangan pakai fitur PHP 7.4+: typed properties, arrow function `fn() =>`,
  `??=`, spread operator untuk array ber-key string, numeric separator `1_000`, `match`, named arguments,
  nullsafe `?->`, union types, `str_contains`/`str_starts_with`, dan sejenisnya.
- **CodeIgniter 3.1.13**. Rujukan: https://codeigniter.com/userguide3/
  - **Bukan CI4.** Jangan pakai namespace `App\`, `BaseController`, `spark`, `$routes->get()`, `.env`,
    `service()`, `view()` gaya CI4, atau skill `ci4*`.
  - Controller: `class Nama extends CI_Controller`, nama file = nama class dengan huruf depan kapital (`Pages.php`).
  - Model: `class Nama_model extends CI_Model`.
  - Routing di `application/config/routes.php` (`$route['slug'] = 'controller/method';`).
  - View dimuat dengan `$this->load->view('path', $data)`.
- Folder ini repo git (branch `main`); commit dilakukan user sendiri. **Jangan commit kecuali diminta.**

## Lingkungan lokal

- **Database:** MariaDB 10.4 (XAMPP, `/opt/lampp`), database `lpstmi_db`, user `root`.
  Konfigurasi di `application/config/database.php`. Hostname **harus `127.0.0.1`**: PHP 7.3 sistem tidak menemukan
  socket XAMPP kalau pakai `localhost`. Charset `utf8mb4`.
- **Web server dev:** `php -d upload_max_filesize=20M -d post_max_size=25M -S localhost:8000 server.php` (PHP 7.3).
  **Jangan pakai Apache XAMPP**, karena PHP-nya 8.2. `server.php` adalah router khusus built-in server: file statis
  dilayani langsung, sisanya ke `index.php`. Opsi `-d` menaikkan batas upload (default PHP hanya 2 MB).
- `base_url` = `http://localhost:8000/`, `index_page` kosong, `.htaccess` sudah ada untuk Apache/produksi.
- Autoload: library `database`; helper `url`, `html`, `wp`. Timezone `Asia/Jakarta` (di awal `config/config.php`).
- Session (file) di `application/cache/sessions/`, hanya dimuat di `/admin`. CSRF aktif (`csrf_token`, tidak diregenerasi).

## Eksplorasi kode (codebase-memory-mcp)

Repo ini sudah diindeks ke knowledge graph **codebase-memory-mcp** (project `home-ughway-Debiancode-Myprojects-lpstmi`).
- **Pakai tool MCP ini dulu** untuk mencari/memahami kode, sebelum Grep/Glob/Read:
  - `search_graph` (cari fungsi/class/method per nama/label/qualified name),
  - `get_code_snippet` (ambil source satu simbol dengan rentang baris yang tepat),
  - `trace_path` (rantai pemanggilan, mis. controller → model → helper),
  - `query_graph` (Cypher untuk pola kompleks), `get_architecture` (gambaran struktur), `search_code` (grep berbasis graph).
- Grep/Glob/Read tetap dipakai untuk file non-kode (config, view HTML hasil clone, CLAUDE.md, SQL, dll.) dan
  **file wajib di-Read dulu sebelum diedit**.
- `stmi.ac.id-clone/` **dikecualikan** dari indeks lewat `.cbmignore` di root (sintaks gitignore). Untuk isi clone,
  pakai Grep/Read biasa.
- Indeks masih memuat `system/` (core CI3) dan `wp-content/`/`wp-includes/` (JS/CSS vendor). Kode proyek yang sebenarnya
  ada di `application/` (+ `assets/admin/`, `server.php`); saring hasil ke path itu (mis. `file_path` diawali `application/`).
- Indeks tidak selalu mengikuti perubahan terbaru: jika hasil tampak usang (fungsi baru tidak ditemukan, baris tidak cocok),
  cek `detect_changes`/`index_status` lalu jalankan `index_repository` ulang. Kalau proyek belum terindeks, `index_repository` dulu.
- `index_repository` bersifat inkremental: menambah pola ke `.cbmignore` **tidak** menghapus node lama. Setelah mengubah
  `.cbmignore`, jalankan `delete_project` lalu `index_repository` supaya indeks dibangun ulang dari nol.

## Struktur folder

```
lpstmi/
├── application/          # kode CI3 (controllers, views, config, ...)
├── system/               # core CI3 — JANGAN DIUBAH
├── index.php             # front controller CI3
├── wp-content/ wp-includes/   # aset hasil salinan dari clone (lihat "Aset"); upload admin masuk ke wp-content/uploads/
├── assets/admin/         # CSS/JS panel admin + TinyMCE
├── server.php            # router untuk php -S (development)
└── stmi.ac.id-clone/     # hasil HTTrack — SUMBER REFERENSI, READ-ONLY
```

### `stmi.ac.id-clone/` itu read-only
- Jangan mengedit, memindah, atau menghapus apa pun di dalamnya. Ini "kunci jawaban" untuk membandingkan tampilan.
- Isinya (~2.900 file, ~500 MB):
  - `<slug>/index.html` → halaman/post WordPress (permalink `/<slug>/`).
  - `indexXXXX.html` di root (mis. `index3592.html`) → varian HTTrack dari URL `?p=<ID>` / query string.
    Cek baris `<!-- Mirrored from https://stmi.ac.id/?p=... -->` untuk tahu URL aslinya. Ini **duplikat**, bukan halaman baru.
  - `category/`, `tag/`, `author/`, `<tahun>/`, `09/` → arsip WordPress (listing post, ada `page/N` untuk paginasi).
  - `download/<slug>/` → halaman plugin Download Manager.
  - `feed/`, `wp-json/`, `xmlrpc*.php` → endpoint dinamis WP, tidak dimigrasi sebagai halaman.
  - `wp-content/themes/blocksy`, `wp-content/plugins/{elementor,blocksy-companion-pro,awsm-team,chaty,download-manager,gtranslate,pdf-embedder,pixel-formbuilder}`, `wp-content/uploads/` → aset.

## Aturan utama: tampilan harus identik

1. **Salin markup apa adanya.** Ambil HTML dari file clone, jangan ditulis ulang, jangan di-"rapikan".
   - Jangan ubah/hapus class (`elementor-*`, `e-con`, `ct-*`, `wp-*`, `data-id`, `data-*`), id, atribut `style` inline, atau urutan elemen.
   - Jangan ubah whitespace di antara elemen inline/inline-block (bisa menggeser layout).
   - Jangan ganti ke Bootstrap/Tailwind/framework lain, jangan "modernisasi" markup.
2. **CSS & JS:** pertahankan semua `<link>`, `<style>` inline, dan `<script>` dengan **urutan yang sama** seperti di clone.
   Jangan menggabung, minify, atau menghapus file CSS/JS meskipun terlihat tidak dipakai.
3. **CSS per halaman ikut dibawa.** Elementor punya CSS khusus per halaman (`wp-content/uploads/elementor/css/post-*.css`)
   dan blok `<style>` inline per halaman. Keduanya harus dimuat di halaman yang sama.
4. **Body class & atribut `<html>`/`<body>`** (mis. `home page-id-490 elementor-page-490 ...`) harus sama dengan clone,
   karena CSS memakainya sebagai selector. Disimpan apa adanya di `body_attrs` (config/pages.php).
5. Satu-satunya perubahan pada markup yang **boleh**:
   - Mengganti path relatif (`../`, `../../`, `index.html`, `slug/index.html`) dengan `base_url()` / `site_url()`.
   - Menghapus komentar HTTrack (`<!-- Mirrored from ... -->`, `<!-- Added by HTTrack -->`). Tag `<meta http-equiv="content-type">` yang ditambahkan HTTrack ikut dihapus karena sudah ada `<meta charset>`.
   - Menandai menu aktif (`current-menu-item`, `current_page_item`, `current-menu-ancestor`, `current-menu-parent`, `aria-current="page"`)
     secara dinamis sesuai halaman, dengan hasil yang sama persis dengan clone halaman tersebut.
   - ID acak dari GTranslate (`gt-wrapper-XXXXXXXX`) boleh dibuat tetap (`73208722`, `35407523`).
   - Menghapus link internal WordPress yang tidak tampil dan endpoint-nya tidak ada di CI:
     `<link rel="https://api.w.org/">`, alternate JSON, `EditURI`, `shortlink`, oEmbed (JSON/XML).
   - URL eksternal yang dirusak HTTrack menjadi path relatif dikembalikan ke bentuk aslinya:
     `../s10.histats.com/` → `//s10.histats.com/`, `../sstatic1.histats.com/0dd88.gif` → `//sstatic1.histats.com/0.gif`,
     `../public.tableau.com/` → `https://public.tableau.com/`.
   - URL absolut ke domain utama (`http(s)://stmi.ac.id/...`, `www.`, `//stmi.ac.id`, dan bentuk JSON `https:\/\/stmi.ac.id\/...`)
     **diubah** (keputusan user), dengan cakupan berikut (`Wp_clone::map_absolute()`):
     - `https://stmi.ac.id/<slug>/` → `site_url('<slug>')`, **hanya jika** `<slug>/index.html` ada di clone.
       `https://stmi.ac.id/` → `site_url()` (kecuali yang ber-query, mis. `?p=`, `?s=`).
     - `https://stmi.ac.id/wp-content|wp-includes/...` → `base_url(...)`, **hanya jika file-nya ada di lokal**.
       Base folder di konfigurasi JS (mis. `public_url` `.../static/bundle/`, `urls.assets` Elementor) ikut diubah jika foldernya ada
       (trailing slash dipertahankan). File yang tidak ada di lokal **tetap ke live**. Unduh dulu, lalu konversi ulang.
     - Bentuk JSON diubah dengan aturan yang sama → `site_url_json()` / `base_url_json()`.
     - **Dibiarkan:** subdomain lain (`jarvis.`, `analytics.` (Matomo), `e-learning.`, `lib.`, `ppid.`, dst.; sistem terpisah),
       endpoint WordPress (`wp-admin/admin-ajax.php`, `wp-json`, `wp-login.php`, `xmlrpc.php`, `feed`; dibahas bersama fitur dinamis),
       dan path yang tidak ada di clone (mis. `/search/QUERY_STRING/`), karena kalau diubah hasilnya 404 di CI.
   Semua perubahan di atas sudah dikerjakan otomatis oleh `application/libraries/Wp_clone.php`.
   Perubahan lain pada markup harus ditanyakan ke user dulu.
6. Kalau ragu apakah perubahan akan memengaruhi tampilan, **jangan diubah**; tanyakan ke user.

## Aset

- `wp-content/` dan `wp-includes/` sudah disalin dari clone ke root proyek **dengan path yang sama** (isi identik,
  sudah dicek dengan `diff -r`). Path harus sama supaya `url(...)` relatif di dalam file CSS tetap jalan.
- Nama file hasil HTTrack (mis. `globalaa62.css`, `main.min6b25.css`) **tetap dipakai apa adanya**; jangan di-rename.
- Jangan menghapus file aset tanpa konfirmasi.
- **File yang dimuat dinamis oleh JS** (tidak ikut ter-clone HTTrack) sudah diunduh dari situs live pada 2026-09-18,
  dengan nama asli di path yang sama (249 file):
  - Blocksy `ct_localizations`: `sticky.js`, `micro-popups.js` (+4 chunk-nya), `non-critical-styles.min.css`,
    `non-critical-search-styles.min.css`, `back-to-top.min.css`, `flexy.min.css`, `cart-header-element-lazy.min.css`,
    `video-lazy.min.css`, `header-account-*-lazy.min.css`.
  - 30 chunk webpack Blocksy (`themes/blocksy/static/bundle/<id>.<hash>.js`, daftar di `main6b25.js`).
  - 22 chunk webpack Elementor (`elementor/assets/js/*.bundle.min.js`, daftar di `webpack.runtime.min55cb.js`).
  - Aset `assetsLoader` Elementor: `lib/dialog/dialog.min.js`, `lib/share-link/share-link.min.js`, `lib/swiper/v8/swiper.min.js`,
    `lib/swiper/v8/css/swiper.min.css`, `css/conditionals/dialog.min.css`, `css/conditionals/lightbox.min.css`.
    (`css/custom-lightbox.min.css` juga 404 di live, jadi tidak ada yang hilang.)
  - PDF Embedder (pdf.js 2.2.228): `pdfjs/pdf.worker.min.js` + 169 file `pdfjs/cmaps/*.bcmap`.
  - `pdfjs/pdf.min277b.worker.js` = salinan `pdf.worker.min.js`. PDF Embedder 4.9.2 tidak mengisi `workerSrc`, jadi pdf.js
    menurunkan nama worker dari nama file skripnya sendiri (`pdf.min.js` → `pdf.worker.min.js`). Karena HTTrack mengganti
    namanya menjadi `pdf.min277b.js`, pdf.js mencari `pdf.min277b.worker.js` ("Setting up fake worker failed").
    Pola yang sama berlaku untuk skrip lain yang menurunkan path dari nama filenya sendiri: sediakan file dengan nama
    turunan tersebut, jangan ubah markup.
  Kalau ada tema/plugin baru yang memuat chunk dari live, unduh dengan cara yang sama (nama asli, jangan menimpa file yang ada).
- **Ukuran gambar di metadata media yang tidak ter-clone** (tidak dipakai di HTML mana pun, tetapi dipakai dinamis, mis. `thumbnail`
  150x150 di live search Blocksy lewat `ct_featured_media`) diunduh dari live pada 2026-09-19: 49 file ukuran (38 `thumbnail`,
  9 `awsm_team`, dll.) + 5 file utama (`-scaled`, `8-2-500x500.jpg`, `professional-networking-illustration-1.png`).
  Semua file di `media.file`/`media.sizes` sekarang ada di lokal; setelah impor media baru, cek lagi dengan skrip yang sama
  (bandingkan `sizes` dengan file di `wp-content/uploads/`), unduh yang hilang dengan nama & path asli.

## Arsitektur CI3

```
application/
├── core/MY_Controller.php             # render(): isi default, load layouts/main, lalu tandai menu aktif
├── controllers/Pages.php              # halaman berkerangka khusus (route per slug dari config/pages.php), home + redirect /?p=ID
├── controllers/Posts.php              # post & arsip kategori/tag/author dari database
├── controllers/admin/                 # panel admin (lihat bagian "Panel admin")
├── models/                            # Post_model, Author_model, Term_model, Media_model
├── libraries/Wp_import.php            # impor post dari clone ke database
├── libraries/Media_uploader.php       # upload + ukuran gambar ala WordPress
├── controllers/Tools.php              # perintah CLI migrasi (convert / reconvert / check / verify)
├── libraries/Wp_clone.php             # logika konversi clone -> view (clean, rewrite URL, split, netralisasi)
├── helpers/wp_helper.php              # port fungsi WordPress: texturize, srcset, paginasi, tanggal, menu aktif, dll.
├── helpers/admin_helper.php           # slugify, unique_slug, auto_excerpt, content_to_tokens, paginasi admin
├── models/Menu_model.php               # menu utama (tabel menu_items): pohon, URL, menu aktif otomatis
├── config/site.php                    # judul situs
├── config/pages.php                   # DIHASILKAN tools: halaman yang masih berupa view (home, error-404)
└── views/
    ├── layouts/main.php               # susunan: document_open, title, head/<varian>, <body>, drawer, header, isi, footer, foot/<varian>
    ├── layouts/partials/document_open.php  # <!doctype> s.d. <title>          (sama di semua halaman)
    ├── layouts/partials/drawer.php    # search modal + menu mobile + <div id="main-container">
    ├── layouts/partials/header.php    # <header id="header"> ... </header>
    ├── layouts/partials/footer.php    # <footer id="footer"> ... </footer>
    ├── layouts/head/<varian>.php      # </title> s.d. sebelum <body: CSS/JS/meta per template
    ├── layouts/foot/<varian>.php      # setelah </footer> s.d. </html>: script per template
    ├── pages/_page.php                # template halaman standar (hero, judul, author, isi, share, sidebar) untuk tabel pages
    └── pages/<slug>.php               # isi halaman khusus: dari setelah </header> s.d. sebelum <footer (termasuk <main>)
```

- **Kenapa head/foot berupa varian:** WordPress hanya memuat CSS/JS yang dipakai tiap halaman, jadi daftar dan urutannya
  berbeda per template (home, page Elementor, post, arsip, download, ...). `tools convert` otomatis memakai ulang varian yang
  isinya identik, dan membuat varian baru kalau belum ada.
- **Menu utama** (header desktop `ul#menu-menu-utama` + mobile `ul#menu-menu-utama-1`) dirender dari tabel `menu_items`
  oleh `wp_nav_menu($main_menu, $mobile)` (port `Walker_Nav_Menu` + markup Blocksy), dipanggil di partial header/drawer.
- **Menu aktif** dihitung otomatis oleh `Menu_model::active_for($menu_context)` (controller mengisi `menu_context`:
  `page` slug / `category` id / `post_categories`), lalu disisipkan `wp_menu_active()` dengan urutan kelas seperti WordPress.
  `config/pages.php` tidak lagi menyimpan `menu_active`.
- Bagian partial yang dinamis (menu utama, kontak, media sosial, link footer) punya padanan "netralisasi" di `Wp_clone`
  (`neutralize_main_menu`, `neutralize_contacts` (juga email Chaty di `chaty_settings` foot), `neutralize_footer_links`) yang dipakai `Tools::prepare()`, supaya
  `tools check/convert/layout` tetap bisa membandingkan clone dengan partial. **Kalau partial diubah jadi dinamis, tambahkan
  netralisasinya juga**, lalu pastikan `tools check` tetap `502 OK`.
- **Atribut gambar di layout** (`fetchpriority="high"`, `loading="lazy"`) berbeda per halaman, jadi disimpan di `img_hints`.
- Isi `data-gt-orig-url` GTranslate = `/<uri>/` (otomatis), `<link rel="canonical">` = `current_url()`.
- **Jangan menambah whitespace/newline** di view layout & halaman: output harus byte-identik.
  `.editorconfig` sudah mematikan `insert_final_newline` untuk `application/views/**`.
- **Awas `?>` diikuti newline:** PHP menelan satu newline setelah `?>`. Di view yang harus byte-identik, tulis `?>\n\n`
  jika memang butuh satu newline di output. Saat mengedit file view/clone dengan skrip, pertahankan `\r\n` (plugin Meta Tag
  Manager memakai CRLF); di Python pakai `open(p, newline='')`.
- File view hasil generate diawali satu baris guard `<?php defined('BASEPATH') ... ?>` (newline setelah `?>` ditelan PHP).
- URL mengikuti permalink WordPress tanpa trailing slash: `/sejarah-kampus`. `/home` di-redirect 301 ke `/`.
- Semua data dinamis (dari DB/input) di-escape dengan `html_escape()`. Markup dari clone dicetak apa adanya.

## Keputusan user

1. **Berita, pengumuman, dan artikel (post WordPress) disimpan di database** `lpstmi_db`. Halaman statis (Page WordPress)
   juga di database (tabel `pages`, dikelola admin & editor; keputusan 2026-09-19), kecuali beranda (view + menu Beranda)
   dan halaman 404. Admin panel (CMS) untuk mengelola post, halaman, media, kategori, tag, dan pengguna.
2. Fitur dinamis (pencarian, form, feed): **nanti**. Markup tetap disalin agar tampilan sama.
   Download Manager **sudah dimigrasi** (lihat bagian "Download Manager").
3. Link absolut ke `https://stmi.ac.id/...`: **diubah** ke `site_url()`/`base_url()` dengan cakupan di aturan 5
   (subdomain lain, endpoint WordPress, dan path yang tidak ada di clone tetap dibiarkan). Termasuk bentuk
   url-encoded di link share (`https%3A%2F%2Fstmi.ac.id%2F...`).

## Database (`lpstmi_db`)

Dibuat dengan CI3 Migrations (`application/migrations/`, `php index.php tools migrate`, versi di `config/migration.php`).
**ID mengikuti ID WordPress** (kelas CSS `post-<ID>`, `ct-term-<ID>`, redirect `/?p=<ID>`).

| Tabel | Isi |
|---|---|
| `authors` | author post sekaligus pengguna admin: `slug` (URL /author/<slug>), `display_name`, `registered_at` (Joined), `gravatar_hash` (sha256 email), `website`, `post_count_offset` (selisih "Articles" WordPress yang ikut menghitung tipe konten lain), `username`, `password_hash` (NULL = tidak bisa login), `role` (admin/editor), `is_active`, `last_login_at` |
| `terms` | `taxonomy` (category/post_tag), `name`, `slug`, `description` |
| `media` | `file` (relatif `wp-content/uploads/`), `width`, `height`, `alt`, `mime_type`, `sizes` (JSON ukuran turunan, **urutan = urutan metadata WordPress**, menentukan urutan srcset) |
| `posts` | `slug`, `title` (**teks mentah**; ditampilkan lewat `wp_texturize()`), `content` (HTML), `excerpt` (HTML kartu arsip), `author_id`, `featured_media_id`, `status` (publish/draft), `published_at`, `modified_at` (waktu lokal), `layout_head`/`layout_foot` (override varian layout; dipakai 3 post Elementor `post-<ID>`) |
| `post_terms` | `post_id`, `term_id`, `term_order` (urutan tampil kategori lalu tag) |
| `pages` | halaman statis (Page WordPress); lihat bagian "Halaman statis dari database" |

- URL situs di `content` disimpan sebagai token `{base_url}`, `{base_url_json}`, `{base_url_encoded}`; diganti saat render
  (`wp_content()`) dan dikembalikan jadi token saat disimpan dari admin (`content_to_tokens()`).
- **Impor ulang** (`php index.php tools import_posts`) mengosongkan `posts`, `post_terms`, `terms`, `media` lalu mengisinya
  dari clone. Data login di `authors` dipertahankan. **Jangan jalankan setelah konten mulai dikelola lewat admin**
  (post baru/hasil edit akan hilang).
- Sumber impor: `stmi.ac.id-clone/wp-json/` + cache REST API live di `application/cache/wp-api/` (18 post & metadata media;
  media 2246 & 2266 privat, disusun dari srcset kartu/navigasi clone) + HTML clone (konten, excerpt, urutan term,
  judul mentah dari navigasi Previous/Next, profil author).
- HTTrack memotong nama folder yang terlalu panjang; slug aslinya dibaca dari komentar "Mirrored from" (`Wp_clone::real_path()`).

## Render post & arsip (`controllers/Posts.php`, `views/posts/`)

Semua aturan ini sudah diverifikasi byte-per-byte terhadap 186 post + 152 halaman arsip di clone:
- Route: `/<slug>` (`Posts::single`: halaman dari tabel `pages` dicek lebih dulu, lalu post; halaman khusus punya route sendiri
  dari `config/pages.php`), `/category/<slug>[/page/N]`,
  `/tag/<slug>[/page/N]`, `/author/<slug>[/page/N]`, `/?p=<ID>` → 301 ke permalink. 5 post per halaman arsip,
  paginasi `paginate_links()` (end_size 1, mid_size 3). `/…/page/1` → 301 ke URL tanpa page.
- Judul: `wp_texturize()` (port wptexturize) untuk h1/kartu; `wp_document_title()` untuk `<title>`; `wp_nav_title()`
  (judul mentah) di navigasi Previous/Next; `wp_attr_title()` untuk `aria-label`; link share `wp_encode_uri_component()`.
- Tanggal: `l, j F Y` (single), `d/m/Y` (kartu, Joined), `c` (atribut datetime).
- Gambar unggulan: `wp_post_thumbnail()` = port `wp_calculate_image_srcset()` (rasio sama, maks 2048, src di depan).
  Kartu arsip `medium_large` + `aspect-ratio: 4/3`, navigasi post `medium` + `1/1`.
- `fetchpriority`/`loading="lazy"` di header & sidebar post: `Posts::single_img_hints()` (port aturan
  wp_get_loading_optimization_attributes: gambar konten ber-width/height dihitung dulu, kecuali post Elementor).
- Varian layout: head `post` (post Elementor: `post-<ID>`), foot `post-pdf` jika konten punya `class="pdfemb-viewer"` atau
  `class='w3eden'` (Download Manager), selain itu `post`; arsip: head `archive` (link feed per term/author), foot `archive`.
- Menu aktif (`Menu_model::active_for()`): halaman statis → item halamannya `current-menu-item page_item page-item-<ID>
  current_page_item`; post → item kategori miliknya `current-post-ancestor current-menu-parent current-post-parent` (induknya
  tidak ditandai); arsip kategori → `current-menu-item`; item URL bebas yang sama dengan URL sekarang → `current-menu-item`.
  Induk langsung item current: `current-menu-ancestor current-menu-parent`, induk di atasnya: `current-menu-ancestor`.
- Body class post Elementor mendapat `elementor-page elementor-page-<ID>`.

## Download Manager (`controllers/Downloads.php`, `views/downloads/single.php`)

- 131 paket (tipe `wpdmpro`) di tabel `downloads` (migrasi 012): `id` (ID WordPress; `?wpdmdl=<ID>`, kelas `post-<ID>`), `slug`
  (`/download/<slug>`), `title` (mentah), `description` (HTML, token `{base_url}`), `template` (`simplified` = "Default Template
  ( Simplified )" / `default` = "Default Template": kartu `[featured_image]` + judul mentah di kolom deskripsi), `button_label`
  (NULL = "Download"), `file` (relatif `wp-content/uploads/` **atau** URL luar), `file_size` (label, mis. "473 KB"),
  `download_count`, `author_id`, `featured_media_id`, `status`, `published_at` (Create Date), `modified_at` (Last Updated).
- Unduh: `/download/<slug>?wpdmdl=<ID>` (juga link lama `/download/<slug>/index<hash>.html?wpdmdl=ID`) → hitungan +1, lalu file lokal
  dikirim (`Content-Disposition: attachment`, path dicek tetap di dalam `wp-content/uploads/`) atau redirect 302 ke URL luar.
  Paket draft / ID tidak ada → 404. Parameter `refresh` hanya anti-cache (acak per halaman, `uniqid().time()`, sama seperti WPDM).
- Impor: `php index.php tools import_downloads [ulang]` (`libraries/Wpdm_import.php`; menolak jika tabel sudah berisi, kecuali `ulang`).
  Sumber file: 64 redirect ke `tro.stmi.ac.id/wp-content/uploads/...` yang file-nya ada di lokal; 5 Google Drive (URL luar);
  7 file yang hanya ada di clone disalin ke `wp-content/uploads/download-manager-files/<slug>.<ext>`; 41 tanpa respons di clone
  **diasumsikan = PDF di deskripsi** (keputusan user; terbukti pada semua paket lain); 2 paket (1298, 1392) redirect-nya rusak
  di situs asli (`...pd` → 404) sehingga dipakai PDF deskripsi.
- Render (diverifikasi 131/131 `verify_db`): head `download`; foot `download-pdf` jika deskripsi berisi `class="pdfemb-viewer"`,
  selain itu `download` (ID paket di skrip view-count WPDM adalah variabel); logo header `fetchpriority` kecuali paket bergambar
  unggulan (maka gambar unggulan yang `fetchpriority` dan avatar author `loading="lazy"`); logo footer tanpa `wp-post-image`.
- **Kartu download di konten** (kartu "WPDM Link Template: Default Template" di halaman & post) disimpan sebagai kode pendek
  `[wpdm_package id='N']` (sama dengan shortcode WPDM) dan dirender saat tampil oleh `Download_model::render_shortcodes()` +
  `views/downloads/_card.php`: judul, ukuran, ikon, teks tombol ikut data paket; paket draft/terhapus tidak tampil; `refresh`
  acak per tampilan. Judul kartu **mentah** di konten biasa (WordPress menjalankan wptexturize sebelum shortcode) dan
  **ter-texturize** di konten Elementor. Ikon: kolom `downloads.icon` (migrasi 016; diisi untuk 4 paket Google Drive berikon PDF),
  NULL = ekstensi file (`file-type-icons/<ext>.svg`, URL luar = `web`). Excerpt pencarian membuang kode pendek (seperti
  `strip_shortcodes()`), dan pencarian tidak lagi cocok dengan judul paket di dalam kartu (sama seperti WordPress).
  - Konversi: `php index.php tools download_shortcodes` (aman diulang) mengganti 124 kartu yang hasil render databasenya
    identik; 7 kartu tetap salinan statis (5 di post berikon dari `tro.stmi.ac.id`, 2 di akreditasi berikon data-URI karena
    file aslinya `.pd`). `verify_db` menormalkan `refresh` untuk post, halaman, dan paket.
  - Editor: tombol **Download** di TinyMCE (post & halaman; `toolbar_mode: 'wrap'`) dan tombol "Sisipkan kartu download" di mode
    HTML mentah membuka pemilih paket (`admin/downloads/browse`, JSON) lalu menyisipkan kode pendek.
- Converter: atribut `data-downloadurl` ikut diubah. Varian HTTrack `index<hash>.html` dipetakan: link `canonical` → halaman itu;
  isi file biner (PDF) → folder paketnya; redirect "Page has moved" ke **halaman** internal → ikuti; selain itu → path asli dari
  komentar "Mirrored from"; varian download yang tidak tersimpan + `?wpdmdl=` → folder paketnya. Query link dipertahankan.

## Panel admin (`/admin`)

- Login: `/admin/login` (username + password, `password_hash`, maks 5 gagal per sesi lalu kunci 5 menit, session
  diregenerasi). Logout hanya via POST. Membuat/mengatur login dari CLI:
  `php index.php tools set_login <slug-author> <username> [admin|editor]` (password acak ditampilkan sekali).
- Peran: **admin** (semua), **editor** (post, media, kategori, tag, profil sendiri; `/admin/users` → 403).
- Controller di `application/controllers/admin/` (`Admin_Controller` di `core/MY_Controller.php`), view di
  `views/admin/`, aset di `assets/admin/` (CSS/JS sendiri, TinyMCE 6.8.5 MIT di `assets/admin/vendor/tinymce/`).
- **Post:** daftar (filter status/kategori/cari), buat/edit (judul mentah, slug unik & tidak bentrok dengan halaman statis
  atau path sistem, konten TinyMCE, excerpt kosong = otomatis 40 kata + "…", kategori wajib ≥1, tag dipisah koma dan
  dibuat otomatis, gambar unggulan dari pustaka media, status, tanggal terbit dengan detik, author), hapus.
  Post Elementor diedit lewat editor blok (atau tab HTML mentah), sama seperti halaman Elementor.
  Menyimpan post hasil impor **tanpa perubahan** menghasilkan tampilan yang tetap identik (sudah diuji). Konten yang
  diedit lewat TinyMCE bisa dinormalisasi oleh editor (atribut/whitespace), itu wajar.
- **Media:** upload (form multi-file, AJAX dari editor & pemilih), maks 20 MB, tipe: gambar (jpg/png/gif/webp) dan dokumen
  (pdf/doc/docx/xls/xlsx/ppt/pptx/zip), file disimpan di `wp-content/uploads/YYYY/MM/`. Gambar dibuatkan ukuran seperti
  WordPress (`libraries/Media_uploader.php`): medium 300, large 1024, thumbnail 150 crop, medium_large 768, 1536, 2048,
  dan `-scaled` jika > 2560 px. Alt text bisa diubah. Hapus media menghapus semua file ukurannya, **kecuali** file-nya (asli
  atau ukuran mana pun) masih dipakai di post, halaman, paket download, tabel beranda, atau view situs
  (`Media_model::content_usage()`): penghapusan ditolak dengan daftar pemakaiannya.
  - Pustaka berisi semua file `wp-content/uploads/YYYY/MM/` (395 item per 2026-09-19): `php index.php tools import_media`
    (aman diulang) mendaftarkan file yang belum ada; ukuran turunan (`-WxH`, `-scaled`) dikelompokkan ke file asli dan diberi
    nama ukuran WordPress (toleransi 1px; lainnya `WxH`), ID & alt diambil dari `<img class="wp-image-N">` di konten/view
    (84 file), sisanya ID baru; `created_at` = awal bulan folder. File `.html` (gambar 404 yang disimpan HTTrack) dilewati.
- **Kategori & tag:** tambah/ubah/hapus (`/admin/terms/index/category|tag`). Kategori baru tidak otomatis masuk menu.
- **Menu** (`/admin/menu`, **khusus admin**): pohon menu utama, tambah/edit item (label, tipe halaman/kategori/URL bebas/label
  tanpa link, induk), naik/turun, tambah sub-item, hapus (sub-item ikut terhapus). **Kedalaman tidak dibatasi** (keputusan user).
  Tabel `menu_items` (migrasi 011): `id` (ID item WordPress untuk 47 item awal; kelas `menu-item-<ID>`), `parent_id`
  (FK ON DELETE CASCADE), `position`, `title` (teks; tampil lewat `wp_texturize()`), `type` (page/category/custom),
  `object_id` (page: ID page WordPress untuk `page-item-<ID>`; category: `terms.id`), `slug` (page; '' = beranda),
  `url` (custom; token `{base_url}`, NULL = tanpa link). Pilihan halaman = semua baris tabel `pages` (beranda = slug '') + halaman
  yang sudah ada di menu tapi belum dimigrasi (ditandai "belum dimigrasi"; link 404 sampai halamannya dibuat).
- **Download** (`/admin/downloads`, admin & editor): daftar (status, cari), buat/edit (judul, slug, file dari pustaka media
  atau URL luar, deskripsi TinyMCE, teks tombol, template, gambar unggulan, status, tanggal dibuat, author), hapus. Ukuran file
  dihitung ulang hanya saat file diganti (label hasil impor dipertahankan). Paket baru berisi PDF dengan deskripsi kosong otomatis
  diberi embed PDF Embedder. "Last Updated" diisi saat disimpan.
- **Pengguna:** tambah/ubah/nonaktifkan/hapus (hanya jika tidak punya post), profil sendiri + ganti password (min. 10 karakter).
- **Halaman** (`/admin/pages`, admin & editor): lihat bagian "Halaman statis dari database".

## Halaman statis dari database (`models/Page_model.php`, `views/pages/_page.php`, `controllers/admin/Pages.php`)

- Tabel `pages` (migrasi 015; dulu `page_index`): `id` (ID page WordPress; kelas `post-<ID>`/`page-id-<ID>`; halaman baru mulai
  20000 agar tidak bentrok dengan ID WordPress), `slug`, `title` (mentah, tampil lewat `wp_texturize()`), `content`, `view`,
  `author_id`, `featured_media_id` (hanya kelas `has-post-thumbnail` + kartu pencarian; hero semua halaman sama), `status`,
  `layout_head`/`layout_foot`, `published_at`, `modified_at`.
- **`view` NULL** (32 halaman, admin & editor mengelola di `/admin/pages`), kolom `template` (migrasi 017):
  - `full-width` (statistik, lowongan-kerja; template WordPress "Elementor Full Width" / `elementor_header_footer`): isi langsung
    di dalam `<main>` (`views/pages/_page_full.php`), tanpa hero/judul/sidebar/share; body class
    `page-template page-template-elementor_header_footer … elementor-template-full-width`, logo header `fetchpriority`.
  - `default`: `content` = isi `entry-content` persis (termasuk
  whitespace sebelum `</div>` penutup), dirender `MY_Controller::render_page()` dengan template `views/pages/_page.php`. Ke-30
  halaman lama (Page WordPress `page-template-default`) memakai kerangka byte-identik yang sama, jadi hanya isi, judul, ID, slug,
  author, dan gambar unggulan yang berbeda. `body_attrs`, `<title>`, link share, dan `img_hints` diturunkan otomatis:
  - `elementor-page elementor-page-<ID>` jika konten Elementor **atau** varian head memuat `elementor-frontend-css`
    (peraturan & perkin: konten biasa, tetapi WordPress tetap memuat Elementor).
  - `img_hints` + atribut avatar author: `Page_model::img_hints()` (aturan loading WordPress: gambar konten ber-width/height
    dihitung dulu, lalu logo sticky, logo default, avatar).
  - Varian layout: `Page_model::layout_for()` mempertahankan varian tersimpan selama memuat aset yang dibutuhkan konten; kalau
    tidak, dipilih otomatis: `pdfemb-viewer` → foot `daftar-isian-penggunaan-anggaran`, video → foot `maklumat-pelayanan`,
    Elementor → head `peraturan` + foot `daftar-informasi`, selain itu head `sejarah-kampus` + foot `akademik`. Halaman Elementor
    hasil impor punya varian head/foot sendiri (CSS `post-<ID>.css`).
- **`view` terisi** (hanya home): tetap file view + `config/pages.php` (dikelola lewat menu Beranda); baris `pages` hanya untuk
  pencarian & pilihan menu, tidak tampil di admin Halaman.
- Admin: judul, slug (unik; tidak boleh sama dengan post, halaman khusus, atau URL sistem, lihat `root_slug_conflict()`; slug
  berubah → item menu ikut diperbarui), isi (TinyMCE; halaman Elementor lewat editor blok atau HTML mentah), status, tanggal, author, gambar
  unggulan. Isi yang tidak diubah disimpan apa adanya (simpan ulang tetap identik, sudah diuji); isi baru diakhiri `"\n\t\t"`
  seperti output WordPress. Hapus ditolak jika halaman masih dipakai item menu. Draft → URL 404.
- **Editor blok Elementor** (`libraries/Elementor_doc.php`, `views/admin/_block_editor.php` + `views/admin/_blocks.php`,
  logika simpan di `Admin_Controller::apply_blocks()`/`image_html()`; tab "Editor blok" / "HTML mentah" di form halaman **dan
  post** Elementor). Generik untuk semua halaman Elementor (tidak ada modul per halaman). HTML **tidak pernah
  diserialisasi ulang**: parser mencatat posisi byte tiap tag, lalu hanya potongan yang berubah yang diganti (simpan tanpa
  perubahan = byte-identik; whitespace tepi isi asli dipertahankan).
  - Field: Teks (`text-editor`, TinyMCE saat tombol "Editor visual" diklik + sisip kartu download), Judul (`heading`), Gambar
    (`image`: pilih dari pustaka media; ukuran mengikuti kelas `size-*` asli, `srcset/sizes` dihitung ulang, `wp-image-<ID>`
    diganti, link lightbox ke file lama ikut diganti), HTML (`html`), Daftar ikon (teks item), Ikon media sosial (link; kosong =
    tanpa `href`), judul Tab (`nested-tabs`) dan judul item Akordeon (`nested-accordion`). Spasi & Form hanya ditampilkan.
  - Aksi per blok/tab/item: naik, turun, duplikat, hapus (tombol `block_op` = `<op>|<key>`; isi field ikut disimpan lebih dulu).
    Kunci aksi = nomor urut blok (`el:<n>`, `item:<n>:<k>`), kunci field = posisi byte; keduanya divalidasi dengan
    `content_hash` (md5 konten tersimpan) agar tidak salah sasaran bila konten berubah dari tab lain.
  - Setelah aksi tab/akordeon, penomoran dibuat ulang seperti Elementor (ID `e-n-tab-title|content-<nomor widget><i>`,
    `data-tab-index`, `aria-selected`, `tabindex`, `e-active`; akordeon `e-n-accordion-item-<prefix><i-1>`, `open` hanya item
    pertama, `data-accordion-index`, `aria-expanded`). Salinan blok yang berisi tab/akordeon mendapat nomor widget baru (ID unik).
  - Diuji: duplikat lalu hapus salinan, turun lalu naik = kembali byte-identik (akordeon, tab, widget); tab hasil duplikat
    berfungsi di halaman publik (JS Elementor).
- Menambah halaman dari clone: `tools convert <slug>` → `tools verify <slug>` → `tools import_pages` (kerangka standar pindah
  ke database dan dihapus dari `config/pages.php`) → hapus `views/pages/<slug>.php` → `tools verify_db page`.

## Halaman 404

- Diambil dari `stmi.ac.id-clone/js15_as.html` (keputusan user): halaman 404 WordPress/Blocksy ("Oops! That page can't be found."
  + form pencarian) yang tersimpan saat HTTrack meminta `/js15_as.js`. Dikonversi sebagai halaman `error-404` di `config/pages.php`
  (`verify_url` = `js15_as.js`; `tools verify error-404` OK). `09/30/index.html` adalah duplikat halaman 404 yang sama.
- Dipakai untuk **semua** 404 publik dengan status HTTP 404: URL tak dikenal (`$route['404_override'] = 'pages/not_found'`) dan
  setiap `show_404()` di controller turunan `MY_Controller` (`core/MY_Exceptions.php` → `MY_Controller::render_not_found()`).
  `show_404()` di controller admin dan CLI tetap memakai halaman 404 bawaan CodeIgniter. `/error-404` sendiri juga 404.
- `data-gt-orig-url` di halaman 404 = `REQUEST_URI` apa adanya (seperti WordPress), bukan `/<uri>/`.
- `js15_as.html` tidak diproses HTTrack, jadi `Wp_clone::clean()` menormalkan dua hal seperti HTTrack agar sama dengan layout
  bersama: link ke domain tanpa path diberi `/` (`http://jarvis.stmi.ac.id` → `.../`), dan `&` di URL piksel Histats → `&amp;`.
- Pembersihan HTTrack sekarang juga membuang baris sisipan `\r\n` sebelum komentar "Mirrored from" (atas & bawah dokumen),
  sehingga semua halaman berakhir `</body>\n</html>\n` dan diawali `<html lang="en-US">\n<head>` seperti output WordPress asli.

## Pencarian (`MY_Controller::render_search()`, `controllers/Search.php`, `models/Search_model.php`, `views/search/`)

- URL: `/?s=<kata>` (Pages::index), `/page/N?s=<kata>`, `/search/<kata>[/page/N]` (format `search_url` Blocksy; `+` = spasi,
  `permitted_uri_chars` ditambah `+`). 5 hasil per halaman; halaman di luar jumlah → 404; `/page/N` tanpa `?s` → 404.
- Yang dicari: post, paket download, dan halaman statis yang terbit (tabel `pages`; untuk halaman khusus, kolom `content` berisi
  konten dari `wp-json/wp/v2/pages`). Port `WP_Query::parse_search()` & `parse_search_order()`: kata dipecah (frasa dalam kutip,
  kata tunggal a-z & stopword Inggris dibuang, `-kata` = pengecualian), tiap kata harus ada di judul atau konten; urutan 1 kata =
  judul memuat kata lalu tanggal; banyak kata = CASE frasa/semua kata/salah satu kata di judul, frasa di konten; lalu tanggal.
- Markup mengikuti halaman hasil pencarian situs live (acuan disimpan di `application/cache/wp-search/`, diambil 2026-09-19):
  hero "Search Results for …", kartu grid (`posts/_card_grid` untuk post, `search/_card_page` dengan excerpt otomatis
  `wp_excerpt_from_html()` 40 kata, `search/_card_download` tanpa excerpt), tanpa hasil = `search-no-results` + form pencarian.
  Varian head `search` (robots `noindex, follow, …`, feed "Search Results for …") dan foot `search` (Matomo `trackSiteSearch`
  dengan kata kunci ter-escape `wp_js_string()` dan jumlah hasil). **Diverifikasi identik** dengan acuan live untuk 5 kueri
  (hasil, urutan, markup; hanya URL paginasi live yang masih `https://stmi.ac.id/page/N/?s=`).
- Live search Blocksy (modal & sidebar): `rest_url` dan `search_url` di konfigurasi JS sekarang diarahkan ke situs ini
  (`Wp_clone::map_absolute()` khusus `wp-json/` dan `search/QUERY_STRING/`). Endpoint `wp-json/wp/v2/search` (Search::rest)
  meniru REST API WordPress: hanya post & halaman (paket download tidak ikut, sama seperti WordPress), field `id`, `title`, `url`,
  `type`, `subtype`, `ct_featured_media.media_details.sizes`, header `X-WP-Total`/`X-WP-TotalPages`. Hasilnya sama dengan live.
- Data live lebih baru dari clone (post/paket baru), jadi hasil pencarian CI bisa berbeda isi dengan live; logikanya sama.

## Fitur dinamis WordPress (belum dimigrasi)

**pixel-formbuilder tidak dipakai di situs** (dicek 2026-09-19): tidak ada form plugin ini di halaman, post, paket download,
JSON WordPress, maupun database (satu-satunya `<form>` di 555 halaman clone adalah form pencarian; `pixelform_form_render`
= 0). Plugin hanya memuat aset global (CSS/JS, `ajax_obj` ke `admin-ajax.php`) dan `<p class="pixelform_form-alert"></p>`
yang tersembunyi. Semua itu sudah ikut tersalin apa adanya, jadi tidak ada yang perlu dimigrasi. Jangan dicari lagi; form baru
(kontak, survei, form builder) adalah fitur baru yang harus disepakati dulu dengan user.

Feed RSS (link feed tetap ada di `<head>` tapi 404), `wp-json` selain pencarian, `xmlrpc`, komentar,
widget Chaty/GTranslate.

## Perintah CLI (`php index.php tools …`, controller `Tools`, hanya bisa dari terminal)

| Perintah | Fungsi |
|---|---|
| `migrate` | jalankan migrasi database |
| `import_posts` | impor post/term/media/author dari clone (mengosongkan tabel konten dulu) |
| `set_login <slug-author> <username> [admin\|editor]` | beri akses login admin |
| `check [folder]` | uji semua halaman clone terhadap layout bersama (tanpa menulis file) |
| `convert <slug> [path]` / `reconvert` | halaman statis clone → view + `config/pages.php` |
| `layout <nama> <path>` | simpan head/foot halaman clone sebagai varian layout bernama |
| `verify [slug\|all]` | bandingkan halaman statis dengan clone |
| `import_downloads [ulang]` | impor 131 paket Download Manager dari clone |
| `import_pages` | pindahkan halaman dari `config/pages.php` ke tabel `pages` (aman diulang; lihat "Halaman statis dari database") |
| `import_media` | daftarkan file `wp-content/uploads/YYYY/MM/` yang belum ada ke pustaka media (aman diulang) |
| `download_shortcodes` | ganti salinan kartu Download Manager di halaman & post dengan `[wpdm_package id='N']` (aman diulang) |
| `verify_db [single-post\|single-wpdmpro\|archive\|page] [detail]` | bandingkan semua post, paket download, arsip & halaman (dari database) dengan clone |

## Alur kerja per halaman statis

```bash
php -S localhost:8000 server.php                 # terminal terpisah
php index.php tools check                        # uji coba semua halaman clone terhadap layout (tanpa menulis file)
php index.php tools convert <slug>               # clone <slug>/index.html -> views/pages/<slug>.php + config/pages.php
php index.php tools convert <slug> path/lain/index.html   # kalau path clone beda dari slug
php index.php tools verify <slug>                # bandingkan render CI dengan clone, byte per byte
php index.php tools verify all
php index.php tools import_pages                 # kerangka standar -> tabel pages (lalu hapus view lamanya)
php index.php tools verify_db page
```

1. `tools convert` gagal kalau header, drawer, footer, atau document_open halaman itu berbeda dari partial bersama.
   Kalau gagal, cari tahu penyebabnya. Jangan "memaksa" dengan mengubah partial tanpa memahami bedanya.
2. `tools verify` harus `OK`, dan tidak boleh ada baris `aset tidak ada`.
3. Cek visual di browser: `http://localhost:8000/<slug>` vs clone (`php -S localhost:8001 -t stmi.ac.id-clone`,
   buka `http://localhost:8001/<slug>/index.html`) di lebar 1440px, 768px, dan 375px. Tidak boleh ada request 404.
4. Satu halaman selesai dan terverifikasi dulu, baru lanjut ke halaman berikutnya.
5. Cek sintaks PHP 7.3: `php -l <file>`.

Status per 2026-09-19: **semua 33 halaman statis** (Page WordPress) sudah dikonversi. 32 halaman dirender dari tabel `pages`
(`verify_db page` OK 32); view `error-404` `verify` OK, `home` sengaja berbeda sejak commit konten beranda dinamis
(blok `<style>` "Override Elementor animation visibility").
Semua 186 post, 152 halaman arsip (kategori, tag, author, dengan paginasi), 131 paket download, dan 32 halaman dirender dari
database; `verify_db` = `OK 501, BEDA 0`.
Setelah mengubah template/helper post atau halaman, **wajib** jalankan `php index.php tools verify_db` (harus `BEDA 0`).
Catatan: varian foot `archive` juga dipakai halaman `sejarah-kampus` (isinya kebetulan identik).
`tools check`: 502 dari 504 halaman clone cocok dengan layout bersama (pengecualian: `feed/` yang berisi XML, dan `09/30` yang merupakan halaman 404).
