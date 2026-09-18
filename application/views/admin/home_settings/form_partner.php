<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $is_create = empty($partner); ?>
<?= form_open($is_create ? 'admin/home_settings/store_partner' : 'admin/home_settings/update_partner/'.$partner['id']) ?>
<div class="editor-layout">
	<div>
		<div class="card">
			<div class="field">
				<label for="image_path">Path Logo (Image Path)</label>
				<input type="text" id="image_path" name="image_path" required maxlength="255" value="<?= $is_create ? '' : html_escape($partner['image_path']) ?>">
				<div class="hint">Contoh: <code>wp-content/uploads/2024/03/Logo-Partner.jpg</code></div>
			</div>

			<div class="field">
				<label for="image_srcset">Gambar Cadangan (Image Srcset - Opsional)</label>
				<input type="text" id="image_srcset" name="image_srcset" value="<?= $is_create ? '' : html_escape($partner['image_srcset']) ?>">
			</div>

			<div class="field">
				<label for="name">Nama Mitra (Opsional)</label>
				<input type="text" id="name" name="name" value="<?= $is_create ? '' : html_escape($partner['name']) ?>">
			</div>

			<div class="field">
				<label for="url">URL Situs Mitra (Opsional)</label>
				<input type="url" id="url" name="url" value="<?= $is_create ? '' : html_escape($partner['url']) ?>">
			</div>
		</div>
	</div>
	
	<div class="editor-side">
		<div class="card">
			<div class="field">
				<label for="order_num">Urutan (Order)</label>
				<input type="number" id="order_num" name="order_num" required value="<?= $is_create ? 0 : html_escape($partner['order_num']) ?>">
			</div>
			
			<button type="submit" class="btn btn-primary" style="width:100%;justify-content:center"><?= $is_create ? 'Tambah Mitra' : 'Simpan Perubahan' ?></button>
			<p style="text-align:center;margin:10px 0 0"><a href="<?= site_url('admin/home_settings?tab=partners') ?>">Batal</a></p>
		</div>

		<?php if (!$is_create): ?>
		<div class="card">
			<label>Pratinjau Logo</label>
			<div style="margin-top: 10px; background: #fff; border: 1px solid #ddd; padding: 10px; border-radius: 5px; text-align: center;">
				<img src="<?= base_url(html_escape($partner['image_path'])) ?>" alt="" style="max-width: 100%; height: auto;">
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>
<?= form_close() ?>
