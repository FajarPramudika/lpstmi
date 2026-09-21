<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $is_create = empty($program); ?>
<?= form_open($is_create ? 'admin/home_settings/store_program' : 'admin/home_settings/update_program/'.$program['id']) ?>
<div class="editor-layout">
	<div>
		<div class="card">
			<div class="field">
				<label for="title">Nama Program Studi</label>
				<input type="text" id="title" name="title" required maxlength="255" value="<?= $is_create ? '' : html_escape($program['title']) ?>">
				<div class="hint">Gunakan <code>&lt;br&gt;</code> untuk mengatur posisi baris baru (enter) pada tampilan kartu.</div>
			</div>
			
			<div class="field">
				<label for="description">Deskripsi Singkat</label>
				<textarea id="description" name="description" rows="4" required><?= $is_create ? '' : html_escape($program['description']) ?></textarea>
			</div>

			<div class="field">
				<label for="url">URL Selengkapnya</label>
				<input type="url" id="url" name="url" required maxlength="255" value="<?= $is_create ? '' : html_escape($program['url']) ?>">
			</div>

			<div class="field">
				<label for="icon_svg">Ikon SVG Mentah (Raw SVG Path)</label>
				<textarea id="icon_svg" name="icon_svg" rows="6" required class="code"><?= $is_create ? '' : html_escape($program['icon_svg']) ?></textarea>
				<div class="hint">Isi dengan elemen path dari SVG Font Awesome atau serupa. Contoh: <code>&lt;path d="..."&gt;&lt;/path&gt;</code></div>
			</div>
		</div>
	</div>
	
	<div class="editor-side">
		<div class="card">
			<div class="field">
				<label for="order_num">Urutan (Order)</label>
				<input type="number" id="order_num" name="order_num" required value="<?= $is_create ? 0 : html_escape($program['order_num']) ?>">
			</div>
			
			<button type="submit" class="btn btn-primary" style="width:100%;justify-content:center"><?= $is_create ? 'Tambah Program Studi' : 'Simpan Perubahan' ?></button>
			<p style="text-align:center;margin:10px 0 0"><a class="btn btn-sm" href="<?= site_url('admin/home_settings?tab=programs') ?>">&larr; Kembali</a></p>
		</div>

		<?php if (!$is_create): ?>
		<div class="card">
			<label>Ikon Saat Ini</label>
			<div style="margin-top: 10px; text-align: center; font-size: 40px; color: #555;">
				<svg aria-hidden="true" width="50" height="50" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><?= $program['icon_svg'] ?></svg>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>
<?= form_close() ?>
