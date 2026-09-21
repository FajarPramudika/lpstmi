<?php defined('BASEPATH') OR exit('No direct script access allowed');
$section = $this->uri->segment(2) ?: 'dashboard';
$is_admin = ($user['role'] === 'admin');

// Kunci aktif: segmen kedua URL admin (terms dibedakan kategori/tag, profil dibedakan dari pengguna).
$current = $section;
if ($section === 'terms') { $current = 'terms:'.($this->uri->segment(4) === 'tag' ? 'tag' : 'category'); }
if ($section === 'users' && $this->uri->segment(3) === 'profile') { $current = 'profile'; }

// Ikon garis 18px (gaya Feather), hanya path SVG.
$icons = array(
	'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
	'post'      => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/>',
	'page'      => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M4 8h16M8 12h8M8 16h5"/>',
	'download'  => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/>',
	'media'     => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>',
	'category'  => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
	'tag'       => '<path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.2"/>',
	'home'      => '<path d="M3 10.5L12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1z"/>',
	'menu'      => '<path d="M4 6h16M4 12h16M4 18h10"/>',
	'link'      => '<path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"/>',
	'contact'   => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
	'users'     => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
	'profile'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/>',
	'external'  => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/>',
	'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
);
$icon = function ($name) use ($icons) {
	return '<svg class="nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$icons[$name].'</svg>';
};

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
<script src="<?= base_url('assets/admin/admin.js') ?>"></script>
<?php if (isset($scripts)) echo $scripts; ?>
</body>
</html>
