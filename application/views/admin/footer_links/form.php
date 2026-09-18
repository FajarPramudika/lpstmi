<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="card" style="max-width:600px">
	<?= form_open() ?>
		<div class="form-group">
			<label class="form-label">Judul</label>
			<input type="text" name="title" class="form-control" value="<?= html_escape($link['title'] ?? '') ?>" required autofocus>
		</div>

		<div class="form-group">
			<label class="form-label">URL</label>
			<input type="text" name="url" class="form-control" value="<?= html_escape($link['url'] ?? '#') ?>" required>
			<div class="help-text">Gunakan # jika hanya sebagai teks, atau masukkan URL lengkap (termasuk http/https).</div>
		</div>

		<div class="form-group">
			<label class="form-label">Urutan</label>
			<input type="number" name="order_num" class="form-control" value="<?= $link['order_num'] ?? 0 ?>" style="width:100px">
			<div class="help-text">Angka lebih kecil tampil lebih dulu.</div>
		</div>

		<div style="margin-top:20px">
			<button type="submit" class="btn btn-primary">Simpan</button>
			<a href="<?= site_url('admin/footer_links/index') ?>" class="btn">Batal</a>
		</div>
	<?= form_close() ?>
</div>
