<?php defined('BASEPATH') OR exit('No direct script access allowed');
$section = $this->uri->segment(2) ?: 'dashboard';
$sub = $this->uri->segment(4);
$nav = array(
	array('dashboard', 'admin', 'Dasbor', TRUE),
	array('posts', 'admin/posts', 'Post', TRUE),
	array('media', 'admin/media', 'Media', TRUE),
	array('terms:category', 'admin/terms/index/category', 'Kategori', TRUE),
	array('terms:tag', 'admin/terms/index/tag', 'Tag', TRUE),
	array('home_settings', 'admin/home_settings', 'Pengaturan Beranda', TRUE),
	array('footer_links', 'admin/footer_links', 'Link Footer', TRUE),
	array('users', 'admin/users', 'Pengguna', $user['role'] === 'admin'),
);
$current = ($section === 'terms') ? 'terms:'.($sub === 'tag' ? 'tag' : 'category') : $section;
if ($section === 'users' && $this->uri->segment(3) === 'profile') { $current = 'profile'; }
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
	<aside class="sidebar">
		<div class="brand">Politeknik STMI<small>Panel admin</small></div>
		<ul class="nav">
			<?php foreach ($nav as $n): if ( ! $n[3]) continue; ?>
			<li><a href="<?= site_url($n[1]) ?>" class="<?= $current === $n[0] ? 'active' : '' ?>"><?= $n[2] ?></a></li>
			<?php endforeach; ?>
			<li class="sep"></li>
			<li><a href="<?= site_url('admin/users/profile') ?>" class="<?= $current === 'profile' ? 'active' : '' ?>">Profil saya</a></li>
			<li><a href="<?= site_url() ?>" target="_blank" rel="noopener">Lihat situs &nearr;</a></li>
			<li class="sep"></li>
			<li><a href="<?= site_url('admin/pages') ?>" <?= isset($menu_pages) ? 'class="active"' : '' ?>>Halaman</a></li>
			<li><a href="<?= site_url('admin/footer_links') ?>" <?= isset($menu_footer_links) ? 'class="active"' : '' ?>>Link Footer</a></li>
			<li><a href="<?= site_url('admin/home_settings') ?>" <?= isset($menu_home_settings) ? 'class="active"' : '' ?>>Beranda Dinamis (Baru)</a></li>
			<li><a href="<?= site_url('admin/contacts') ?>" <?= isset($menu_contacts) ? 'class="active"' : '' ?>>Kontak</a></li>
		</ul>
		<div class="sidebar-foot">
			<div><?= html_escape($user['display_name']) ?> <span class="muted">(<?= $user['role'] ?>)</span></div>
			<?= form_open('admin/logout', array('class' => 'inline')) ?><button type="submit">Keluar</button><?= form_close() ?>
		</div>
	</aside>
	<main class="main">
		<div class="page-head">
			<div style="display:flex;gap:10px;align-items:center">
				<button type="button" class="btn btn-sm menu-toggle" aria-label="Menu">&#9776;</button>
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
