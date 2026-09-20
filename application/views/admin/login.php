<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!doctype html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title>Masuk &lsaquo; Admin Politeknik STMI Jakarta</title>
	<link rel="stylesheet" href="<?= base_url('assets/admin/admin.css') ?>">
</head>
<body>
<div class="login-wrap">
	<div class="login-card card">
		<div class="brand-login"><b>Politeknik STMI Jakarta</b><span class="muted">Panel admin</span></div>
		<?php if ($error): ?><div class="alert alert-error"><?= html_escape($error) ?></div><?php endif; ?>
		<?= form_open('admin/login') ?>
			<div class="field">
				<label for="username">Username atau email</label>
				<input type="text" id="username" name="username" autocomplete="username" required autofocus value="<?= html_escape((string) $this->input->post('username')) ?>">
			</div>
			<div class="field">
				<label for="password">Password</label>
				<input type="password" id="password" name="password" autocomplete="current-password" required>
			</div>
			<button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Masuk</button>
		<?= form_close() ?>
		<p class="hint" style="text-align:center;margin-top:14px"><a href="<?= site_url() ?>">&larr; Kembali ke situs</a></p>
	</div>
</div>
</body>
</html>
