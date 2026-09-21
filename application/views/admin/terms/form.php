<?php defined('BASEPATH') OR exit('No direct script access allowed'); $is_new = empty($term['id']); ?>
<div class="form-back-row"><a class="btn form-back" href="<?= site_url('admin/terms/index/'.$type) ?>">&larr; Kembali ke <?= html_escape($tax['label']) ?></a></div>
<div class="card" style="max-width:640px">
	<?= form_open($is_new ? 'admin/terms/create/'.$type : 'admin/terms/edit/'.$type.'/'.$term['id']) ?>
		<div class="field">
			<label for="name">Nama</label>
			<input type="text" id="name" name="name" required maxlength="200" data-slug-source value="<?= html_escape(isset($term['name']) ? $term['name'] : '') ?>">
		</div>
		<div class="field">
			<label for="slug">Slug</label>
			<input type="text" id="slug" name="slug" data-slug-target value="<?= html_escape(isset($term['slug']) ? $term['slug'] : '') ?>" placeholder="otomatis dari nama">
			<div class="hint">URL: <?= site_url($tax['path'].'/') ?>&lt;slug&gt;. Mengubah slug membuat link lama tidak berlaku.</div>
		</div>
		<div class="field">
			<label for="description">Deskripsi</label>
			<textarea id="description" name="description" rows="3"><?= html_escape(isset($term['description']) ? $term['description'] : '') ?></textarea>
		</div>
		<button type="submit" class="btn btn-primary">Simpan</button>
	<?= form_close() ?>
</div>
