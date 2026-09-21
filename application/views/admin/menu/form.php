<?php defined('BASEPATH') OR exit('No direct script access allowed');
$is_new = empty($item['id']);
$type = isset($item['type']) ? $item['type'] : 'page';
?>
<div class="card" style="max-width:720px">
	<?= form_open($is_new ? 'admin/menu/create' : 'admin/menu/edit/'.$item['id']) ?>
		<div class="field">
			<label for="title">Label</label>
			<input type="text" id="title" name="title" required maxlength="255" value="<?= html_escape(isset($item['title']) ? $item['title'] : '') ?>">
			<div class="hint">Teks yang tampil di menu (menu situs memakai huruf kapital, mis. "SEJARAH KAMPUS").</div>
		</div>
		<div class="field">
			<label for="type">Tipe</label>
			<select id="type" name="type" data-menu-type>
				<?php foreach ($type_labels as $key => $label): ?>
				<option value="<?= $key ?>" <?= $type === $key ? 'selected' : '' ?>><?= $label ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="field" data-for-type="page">
			<label for="page">Halaman</label>
			<select id="page" name="page">
				<?php foreach ($pages as $slug => $p): ?>
				<option value="<?= html_escape($slug) ?>" <?= ($type === 'page' && isset($item['slug']) && $item['slug'] === (string) $slug) ? 'selected' : '' ?>>
					<?= html_escape($p['title']) ?> (/<?= html_escape($slug) ?>)<?= $p['migrated'] ? ($p['status'] === 'draft' ? ' — draft' : '') : ' — belum dimigrasi' ?>
				</option>
				<?php endforeach; ?>
			</select>
			<div class="hint">Halaman dari menu <a href="<?= site_url('admin/pages') ?>">Halaman</a>. Link ke halaman draft atau yang belum dimigrasi 404 sampai halamannya diterbitkan/dibuat.</div>
		</div>
		<div class="field" data-for-type="category">
			<label for="category">Kategori</label>
			<select id="category" name="category">
				<?php foreach ($categories as $c): ?>
				<option value="<?= $c['id'] ?>" <?= ($type === 'category' && isset($item['object_id']) && (int) $item['object_id'] === (int) $c['id']) ? 'selected' : '' ?>><?= html_escape($c['name']) ?></option>
				<?php endforeach; ?>
			</select>
			<div class="hint">Menu ditandai aktif di halaman arsip kategori ini dan di post-post dalam kategori ini.</div>
		</div>
		<div class="field" data-for-type="custom">
			<label for="url">URL</label>
			<input type="text" id="url" name="url" value="<?= html_escape($url_input) ?>" placeholder="https://… atau /halaman atau #">
			<div class="hint">Kosongkan untuk label tanpa link (biasanya induk dropdown, mis. "PROFILE"). Isi <code>#</code> untuk link kosong.
				Link ke situs ini boleh ditulis <code>/slug</code>.</div>
		</div>
		<div class="field">
			<label for="parent_id">Induk</label>
			<select id="parent_id" name="parent_id">
				<option value="">— Level teratas —</option>
				<?php foreach ($parents as $p): ?>
				<option value="<?= $p['id'] ?>" <?= (isset($item['parent_id']) && (int) $item['parent_id'] === (int) $p['id']) ? 'selected' : '' ?>><?= str_repeat('&nbsp;&nbsp;&nbsp;', $p['depth']) ?><?= html_escape($p['title']) ?></option>
				<?php endforeach; ?>
			</select>
			<div class="hint">Tidak ada batas kedalaman. Item ini dan sub-itemnya tidak bisa dipilih sebagai induk.</div>
		</div>
		<button type="submit" class="btn btn-primary">Simpan</button>
		<a class="btn" href="<?= site_url('admin/menu') ?>">&larr; Kembali</a>
	<?= form_close() ?>
</div>
<script>
(function () {
	var select = document.querySelector('[data-menu-type]');
	function sync() {
		document.querySelectorAll('[data-for-type]').forEach(function (el) {
			el.hidden = el.getAttribute('data-for-type') !== select.value;
		});
	}
	select.addEventListener('change', sync);
	sync();
})();
</script>
