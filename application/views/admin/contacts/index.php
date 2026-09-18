<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="card" style="max-width: 600px">
	<?= form_open('admin/contacts') ?>
		<div class="form-group">
			<label>Email</label>
			<input type="email" name="email" class="form-control" value="<?= html_escape($contacts['email']) ?>" required>
			<p class="hint">Contoh: humas@stmi.ac.id</p>
		</div>
		<div class="form-group">
			<label>Telepon (Phone)</label>
			<input type="text" name="phone" class="form-control" value="<?= html_escape($contacts['phone']) ?>" required>
			<p class="hint">Contoh: 021-42888206</p>
		</div>
		<div class="form-group">
			<label>WhatsApp (Teks Tampilan)</label>
			<input type="text" name="whatsapp" class="form-control" value="<?= html_escape($contacts['whatsapp']) ?>" required>
			<p class="hint">Teks yang akan tampil di situs. Contoh: 0851-552-44455</p>
		</div>
		<div class="form-group">
			<label>WhatsApp URL</label>
			<input type="url" name="whatsapp_url" class="form-control" value="<?= html_escape($contacts['whatsapp_url']) ?>" required>
			<p class="hint">URL tujuan saat WhatsApp diklik. Biasanya diawali dengan https://web.whatsapp.com/send?phone=</p>
		</div>
		<hr style="margin:20px 0;border:0;border-top:1px solid #ddd">
		<h3>Tautan Sosial Media</h3>
		<div class="form-group">
			<label>X (Twitter) URL</label>
			<input type="url" name="social_twitter" class="form-control" value="<?= html_escape($contacts['social_twitter'] ?? '') ?>">
		</div>
		<div class="form-group">
			<label>Instagram URL</label>
			<input type="url" name="social_instagram" class="form-control" value="<?= html_escape($contacts['social_instagram'] ?? '') ?>">
		</div>
		<div class="form-group">
			<label>Facebook URL</label>
			<input type="url" name="social_facebook" class="form-control" value="<?= html_escape($contacts['social_facebook'] ?? '') ?>">
		</div>
		<div class="form-group">
			<label>YouTube URL</label>
			<input type="url" name="social_youtube" class="form-control" value="<?= html_escape($contacts['social_youtube'] ?? '') ?>">
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
		</div>
	<?= form_close() ?>
</div>
