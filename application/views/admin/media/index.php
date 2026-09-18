<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="card upload-box">
	<?= form_open_multipart('admin/media/upload') ?>
		<label for="files">Upload file</label>
		<input type="file" id="files" name="files[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip">
		<button type="submit" class="btn btn-primary">Upload</button>
		<div class="hint">Gambar (JPG, PNG, GIF, WebP) dibuat otomatis dalam ukuran seperti WordPress (150, 300, 768, 1024, 1536, 2048 px). Dokumen: PDF, Word, Excel, PowerPoint, ZIP. Maks. 20 MB per file.</div>
	<?= form_close() ?>
</div>
<form class="filters" method="get" action="<?= site_url('admin/media') ?>">
	<select name="type">
		<option value="">Semua tipe</option>
		<option value="image" <?= $type === 'image' ? 'selected' : '' ?>>Gambar</option>
		<option value="file" <?= $type === 'file' ? 'selected' : '' ?>>Dokumen</option>
	</select>
	<input type="search" name="q" value="<?= html_escape($q) ?>" placeholder="Cari nama file…">
	<button class="btn" type="submit">Filter</button>
	<span class="muted" style="margin-left:auto"><?= $total ?> file</span>
</form>
<?php $back = admin_url_query('admin/media', array('type' => $type, 'q' => $q, 'page' => $page > 1 ? $page : NULL));
$back = substr($back, strlen(site_url())); ?>
<div class="media-grid">
	<?php if (empty($items)): ?><p class="muted">Belum ada media.</p><?php endif; ?>
	<?php foreach ($items as $m): $u = Media_model::urls($m); $is_image = strpos($m['mime_type'], 'image/') === 0; ?>
	<div class="media-item">
		<a class="media-thumb" href="<?= $u['url'] ?>" target="_blank" rel="noopener">
			<?php if ($is_image): ?><img src="<?= $u['thumb'] ?>" alt="<?= html_escape($m['alt']) ?>" loading="lazy">
			<?php else: ?><span class="ext"><?= html_escape(pathinfo($m['file'], PATHINFO_EXTENSION)) ?></span><?php endif; ?>
		</a>
		<div class="media-meta">
			<div><?= html_escape(basename($m['file'])) ?></div>
			<?php if ($is_image): ?><div class="muted"><?= $m['width'] ?> &times; <?= $m['height'] ?></div><?php endif; ?>
			<details>
				<summary>Detail</summary>
				<input type="text" readonly value="<?= $u['url'] ?>" onclick="this.select()" aria-label="URL file">
				<?php if ($is_image): ?>
				<?= form_open('admin/media/update/'.$m['id']) ?>
					<input type="hidden" name="back" value="<?= html_escape($back) ?>">
					<input type="text" name="alt" value="<?= html_escape($m['alt']) ?>" placeholder="Alt text" aria-label="Alt text">
					<button type="submit" class="btn btn-sm">Simpan alt</button>
				<?= form_close() ?>
				<?php endif; ?>
				<?= form_open('admin/media/delete/'.$m['id'], array('data-confirm' => 'Hapus file ini beserta semua ukurannya? Link ke file ini di konten akan rusak.')) ?>
					<input type="hidden" name="back" value="<?= html_escape($back) ?>">
					<button type="submit" class="btn-link danger" style="margin-top:6px">Hapus</button>
				<?= form_close() ?>
			</details>
		</div>
	</div>
	<?php endforeach; ?>
</div>
<?= admin_pagination($page, $total_pages, function ($n) use ($type, $q) {
	return admin_url_query('admin/media', array('type' => $type, 'q' => $q, 'page' => $n > 1 ? $n : NULL));
}) ?>
