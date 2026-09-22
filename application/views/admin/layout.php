<?php defined('BASEPATH') OR exit('No direct script access allowed');
$section = $this->uri->segment(2) ?: 'dashboard';
$is_admin = ($user['role'] === 'admin');

// Kunci aktif: segmen kedua URL admin (terms dibedakan kategori/tag, profil dibedakan dari pengguna).
$current = $section;
if ($section === 'terms') { $current = 'terms:'.($this->uri->segment(4) === 'tag' ? 'tag' : 'category'); }
if ($section === 'users' && $this->uri->segment(3) === 'profile') { $current = 'profile'; }

// Ikon garis 18px (gaya Feather): admin_icon() di helpers/admin_helper.php.
$icon = 'admin_icon';

// Kelompok menu: judul => daftar [kunci aktif, URL, label, ikon, tampil?]
$nav = array(
	'' => array(
		array('dashboard', 'admin', 'Dasbor', 'dashboard', TRUE),
	),
	'Konten' => array(
		array('posts', 'admin/posts', 'Post', 'post', TRUE),
		array('pages', 'admin/pages', 'Halaman', 'page', TRUE),
		array('downloads', 'admin/downloads', 'Download', 'download', TRUE),
		array('media', 'admin/media', 'Media', 'media', TRUE),
		array('terms:category', 'admin/terms/index/category', 'Kategori', 'category', TRUE),
		array('terms:tag', 'admin/terms/index/tag', 'Tag', 'tag', TRUE),
	),
	'Tampilan situs' => array(
		array('home_settings', 'admin/home_settings', 'Beranda', 'home', TRUE),
		array('menu', 'admin/menu', 'Menu', 'menu', $is_admin),
		array('footer_links', 'admin/footer_links', 'Link footer', 'link', $is_admin),
		array('contacts', 'admin/contacts', 'Kontak & media sosial', 'contact', $is_admin),
	),
	'Akun' => array(
		array('users', 'admin/users', 'Pengguna', 'users', $is_admin),
		array('profile', 'admin/users/profile', 'Profil saya', 'profile', TRUE),
	),
);
$words = preg_split('/\s+/', trim($user['display_name']));
$initials = strtoupper(substr($words[0], 0, 1).(count($words) > 1 ? substr(end($words), 0, 1) : ''));
$flash = $this->session->flashdata('flash');
?><!doctype html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<meta name="admin-base" content="<?= base_url() ?>">
	<meta name="csrf-name" content="<?= $this->security->get_csrf_token_name() ?>">
	<meta name="csrf-hash" content="<?= $this->security->get_csrf_hash() ?>">
	<title><?= html_escape($title) ?> &lsaquo; Admin Politeknik STMI Jakarta</title>
	<link rel="stylesheet" href="<?= base_url('assets/admin/admin.css') ?>">
</head>
<body>
<div class="app">
	<aside class="sidebar" id="admin-sidebar">
		<a class="brand" href="<?= site_url('admin') ?>">
			<span class="brand-mark" aria-hidden="true">S</span>
			<span>Politeknik STMI<small>Panel admin</small></span>
		</a>
		<nav class="nav" aria-label="Menu admin">
			<?php foreach ($nav as $group => $items):
				$items = array_filter($items, function ($n) { return $n[4]; });
				if (empty($items)) continue; ?>
			<div class="nav-group">
				<?php if ($group !== ''): ?><div class="nav-title"><?= $group ?></div><?php endif; ?>
				<ul>
					<?php foreach ($items as $n): ?>
					<li><a href="<?= site_url($n[1]) ?>"<?= $current === $n[0] ? ' class="active" aria-current="page"' : '' ?>><?= $icon($n[3]) ?><span><?= $n[2] ?></span></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endforeach; ?>
		</nav>
		<div class="sidebar-foot">
			<div class="user-card">
				<span class="avatar" aria-hidden="true"><?= html_escape($initials) ?></span>
				<span class="user-meta">
					<strong><?= html_escape($user['display_name']) ?></strong>
					<small><?= $is_admin ? 'Admin' : 'Editor' ?></small>
				</span>
			</div>
			<div class="foot-actions">
				<a href="<?= site_url() ?>" target="_blank" rel="noopener"><?= $icon('external') ?><span>Lihat situs</span></a>
				<?= form_open('admin/logout', array('class' => 'inline')) ?><button type="submit"><?= $icon('logout') ?><span>Keluar</span></button><?= form_close() ?>
			</div>
		</div>
	</aside>
	<div class="sidebar-backdrop" data-sidebar-close hidden></div>
	<main class="main">
		<div class="page-head">
			<div style="display:flex;gap:10px;align-items:center">
				<button type="button" class="btn btn-sm menu-toggle" aria-label="Buka menu" aria-controls="admin-sidebar" aria-expanded="false">&#9776;</button>
				<h1><?= html_escape($title) ?></h1>
			</div>
			<?php if (isset($actions)) echo $actions; ?>
		</div>
		<?php if ($flash): ?>
		<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>"><?= html_escape($flash['message']) ?></div>
		<?php endif; ?>
		<?php if ( ! empty($errors)): ?>
		<div class="alert alert-error"><ul><?php foreach ($errors as $err): ?><li><?= html_escape($err) ?></li><?php endforeach; ?></ul></div>
		<?php endif; ?>
		<?php $this->load->view($content_view); ?>
	</main>
</div>

<div class="modal" id="media-modal" role="dialog" aria-modal="true" aria-label="Pilih media">
	<div class="modal-box">
		<div class="modal-head">
			<strong>Pustaka media</strong>
			<div style="display:flex;gap:8px;align-items:center">
				<input type="search" placeholder="Cari nama file…" data-search style="width:200px">
				<label class="btn btn-sm" style="margin:0;font-weight:400">Upload<input type="file" data-upload multiple hidden accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip"></label>
			</div>
		</div>
		<div class="modal-body"><div class="media-grid"></div></div>
		<div class="modal-foot">
			<div class="pager" style="margin:0"><button type="button" class="btn btn-sm" data-prev>&larr;</button><span data-page-info></span><button type="button" class="btn btn-sm" data-next>&rarr;</button></div>
			<div><button type="button" class="btn" data-close>Batal</button> <button type="button" class="btn btn-primary" data-choose>Pilih</button></div>
		</div>
	</div>
</div>
<div class="modal confirm-modal" id="delete-confirm-modal" role="alertdialog" aria-modal="true" aria-labelledby="delete-confirm-title" aria-describedby="delete-confirm-message">
	<div class="modal-box">
		<div class="confirm-body">
			<div class="confirm-icon" aria-hidden="true">!</div>
			<div>
				<h2 id="delete-confirm-title">Hapus data?</h2>
				<p id="delete-confirm-message" data-confirm-message></p>
			</div>
		</div>
		<div class="modal-foot confirm-actions">
			<button type="button" class="btn" data-confirm-cancel>Batal</button>
			<button type="button" class="btn btn-confirm-delete" data-confirm-approve>Ya, hapus</button>
		</div>
	</div>
</div>
<script src="<?= base_url('assets/admin/admin.js') ?>"></script>
<?php if (isset($scripts)) echo $scripts; ?>
</body>
</html>
