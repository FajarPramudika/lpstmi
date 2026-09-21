<?php defined('BASEPATH') OR exit('No direct script access allowed');
$is_new = empty($download['id']);
$v = function ($key, $default = '') use ($download) { return isset($download[$key]) ? $download[$key] : $default; };
$published_at = $v('published_at') ? date('Y-m-d\TH:i:s', strtotime($v('published_at'))) : date('Y-m-d\TH:i:s');
?>
<?= form_open($is_new ? 'admin/downloads/create' : 'admin/downloads/edit/'.$download['id']) ?>
<div class="editor-layout">
	<div>
		<div class="card">
			<div class="field">
				<label for="title">Judul</label>
				<input type="text" id="title" name="title" class="title-input" required maxlength="500" data-slug-source value="<?= html_escape($v('title')) ?>">
			</div>
			<div class="field">
				<label for="slug">Slug URL</label>
				<input type="text" id="slug" name="slug" data-slug-target value="<?= html_escape($v('slug')) ?>" placeholder="otomatis dari judul">
				<div class="permalink"><?= site_url('download/') ?><strong><?= html_escape($v('slug', '…')) ?></strong></div>
			</div>
			<div class="field">
				<label for="file">File</label>
				<div style="display:flex;gap:8px">
					<input type="text" id="file" name="file" value="<?= html_escape($file_input) ?>" placeholder="pilih dari pustaka media, atau URL https://…" required>
					<button type="button" class="btn" data-file-pick>Pilih file</button>
				</div>
				<div class="hint">File dari pustaka media (atau upload baru lewat tombol "Pilih file"), atau URL lengkap file di situs lain (mis. Google Drive).
					Ukuran file dihitung otomatis saat file diganti.<?php if ( ! $is_new && $v('file_size') !== ''): ?> Ukuran sekarang: <strong><?= html_escape($v('file_size')) ?></strong>.<?php endif; ?></div>
			</div>
			<div class="field">
				<label for="content">Deskripsi</label>
				<textarea id="content" name="description" rows="10"><?= html_escape($description) ?></textarea>
				<div class="hint">Tampil di sebelah tombol Download. Paket baru berisi PDF dengan deskripsi kosong otomatis menampilkan PDF-nya (PDF Embedder), seperti paket lama.</div>
			</div>
		</div>
	</div>
	<div class="editor-side">
		<div class="card">
			<div class="field">
				<label for="status">Status</label>
				<select id="status" name="status">
					<option value="publish" <?= $v('status') === 'publish' ? 'selected' : '' ?>>Terbit</option>
					<option value="draft" <?= $v('status', 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
				</select>
			</div>
			<div class="field">
				<label for="published_at">Tanggal dibuat</label>
				<input type="datetime-local" id="published_at" name="published_at" value="<?= $published_at ?>" step="1" required>
				<div class="hint">"Last Updated" otomatis diisi saat disimpan.</div>
			</div>
			<div class="field">
				<label for="author_id">Author</label>
				<select id="author_id" name="author_id">
					<?php $selected_author = (int) $v('author_id', $user['id']); ?>
					<?php foreach ($authors as $id => $name): ?>
					<option value="<?= $id ?>" <?= $selected_author === (int) $id ? 'selected' : '' ?>><?= html_escape($name) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Simpan</button>
			<p style="text-align:center;margin:10px 0 0"><a class="btn btn-sm" href="<?= site_url('admin/downloads') ?>">&larr; Kembali ke Download</a></p>
			<?php if ( ! $is_new && $v('status') === 'publish'): ?>
			<p style="text-align:center;margin:10px 0 0"><a href="<?= site_url('download/'.$download['slug']) ?>" target="_blank" rel="noopener">Lihat halaman &nearr;</a></p>
			<?php endif; ?>
		</div>
		<div class="card">
			<div class="field">
				<label for="button_label">Teks tombol</label>
				<input type="text" id="button_label" name="button_label" maxlength="255" value="<?= html_escape($v('button_label')) ?>" placeholder="Download">
			</div>
			<div class="field" style="margin:0">
				<label for="template">Template</label>
				<select id="template" name="template">
					<option value="simplified" <?= $v('template', 'simplified') === 'simplified' ? 'selected' : '' ?>>Sederhana (tanpa gambar)</option>
					<option value="default" <?= $v('template') === 'default' ? 'selected' : '' ?>>Default (judul + gambar unggulan)</option>
				</select>
			</div>
		</div>
		<div class="card" data-featured>
			<label>Gambar unggulan</label>
			<input type="hidden" name="featured_media_id" value="<?= $featured ? $featured['id'] : '' ?>">
			<div class="featured-preview">
				<?php if ($featured): $u = Media_model::urls($featured); ?><img src="<?= $u['thumb'] ?>" alt="">
				<?php else: ?><span class="muted">Belum ada gambar</span><?php endif; ?>
			</div>
			<button type="button" class="btn btn-sm" data-featured-pick>Pilih gambar</button>
			<button type="button" class="btn btn-sm btn-danger" data-featured-remove <?= $featured ? '' : 'hidden' ?>>Lepas</button>
			<div class="hint">Hanya tampil di template Default.</div>
		</div>
		<?php if ( ! $is_new): ?>
		<div class="card">
			<div class="hint" style="margin:0">Diunduh <strong><?= number_format($download['download_count'], 0, ',', '.') ?></strong> kali · Diubah <?= date('d/m/Y H:i', strtotime($download['modified_at'])) ?></div>
		</div>
		<?php endif; ?>
	</div>
</div>
<?= form_close() ?>
<?php if ( ! $is_new): ?>
<?= form_open('admin/downloads/delete/'.$download['id'], array('data-confirm' => 'Hapus paket ini? Tombol Download yang menunjuk ke paket ini di post/halaman lain akan 404.')) ?>
	<button type="submit" class="btn btn-danger">Hapus paket</button>
<?= form_close() ?>
<?php endif; ?>
<script>
document.querySelector('[data-file-pick]').addEventListener('click', function () {
	window.openMediaPicker({ type: 'all', onSelect: function (item) { document.getElementById('file').value = item.url; } });
});
</script>
