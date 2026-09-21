<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $is_create = empty($link); ?>
<?= form_open($is_create ? 'admin/home_settings/store_link' : 'admin/home_settings/update_link/'.$link['id']) ?>
<div class="editor-layout">
	<div>
		<div class="card">
			<div class="field">
				<label for="url">URL Layanan</label>
				<input type="url" id="url" name="url" required maxlength="255" value="<?= $is_create ? '' : html_escape($link['url']) ?>">
			</div>
			
			<div class="field">
				<label for="image_path">Path Gambar (Image Path)</label>
				<div style="display:flex;gap:8px">
					<input type="text" id="image_path" name="image_path" required maxlength="255" value="<?= $is_create ? '' : html_escape($link['image_path']) ?>">
					<button type="button" class="btn" data-home-image-picker>Unggah / Pilih gambar</button>
				</div>
				<div class="hint">Unggah gambar baru atau pilih dari pustaka media. Path dapat diisi manual bila diperlukan.</div>
			</div>

			<div class="field">
				<label for="image_srcset">Gambar Cadangan (Image Srcset - Opsional)</label>
				<input type="text" id="image_srcset" name="image_srcset" value="<?= $is_create ? '' : html_escape($link['image_srcset']) ?>">
				<div class="hint">Kosongkan jika tidak ada. Contoh: <code>wp-content/uploads/2024/03/E-Learning-STMI.png 348w, ...</code></div>
			</div>

			<div class="field">
				<label for="image_class">Kelas CSS Gambar (Image Class)</label>
				<input type="text" id="image_class" name="image_class" required value="<?= $is_create ? 'attachment-large size-large wp-image-1135' : html_escape($link['image_class']) ?>">
			</div>

			<div class="field">
				<label for="img_hint_key">Kunci Hint Gambar (Opsional)</label>
				<input type="text" id="img_hint_key" name="img_hint_key" value="<?= $is_create ? '' : html_escape($link['img_hint_key']) ?>">
				<div class="hint">Contoh: <code>home:0</code>. Kosongkan jika tidak ada.</div>
			</div>
		</div>
	</div>
	
	<div class="editor-side">
		<div class="card">
			<div class="field">
				<label for="order_num">Urutan (Order)</label>
				<input type="number" id="order_num" name="order_num" required value="<?= $is_create ? 0 : html_escape($link['order_num']) ?>">
			</div>

			<div class="field">
				<label for="row_number">Baris (Row)</label>
				<input type="number" id="row_number" name="row_number" required min="1" value="<?= $is_create ? 1 : html_escape($link['row_number']) ?>">
				<div class="hint">Nomor baris tampilan. Item di baris sama otomatis menyesuaikan ukuran.</div>
			</div>
			
			<button type="submit" class="btn btn-primary" style="width:100%;justify-content:center"><?= $is_create ? 'Tambah Layanan' : 'Simpan Perubahan' ?></button>
			<p style="text-align:center;margin:10px 0 0"><a class="btn btn-sm" href="<?= site_url('admin/home_settings?tab=links') ?>">&larr; Kembali</a></p>
		</div>

		<?php if (!$is_create): ?>
		<div class="card">
			<label>Pratinjau Gambar Saat Ini</label>
			<div data-home-image-preview style="margin-top: 10px; background: #0b5394; padding: 10px; border-radius: 5px; text-align: center;">
				<img src="<?= base_url(html_escape($link['image_path'])) ?>" alt="" style="max-width: 100%; max-height: 100px;">
			</div>
		</div>
		<?php endif; ?>
		<?php if ($is_create): ?><div class="card"><label>Pratinjau gambar</label><div data-home-image-preview style="margin-top:10px;text-align:center"></div></div><?php endif; ?>
	</div>
</div>
<?= form_close() ?>
