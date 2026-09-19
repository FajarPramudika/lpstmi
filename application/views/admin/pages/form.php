<?php defined('BASEPATH') OR exit('No direct script access allowed');
$is_new = empty($page['id']);
$published_at = ! empty($page['published_at']) ? date('Y-m-d\TH:i:s', strtotime($page['published_at'])) : date('Y-m-d\TH:i:s');
?>
<?= form_open($is_new ? 'admin/pages/create' : 'admin/pages/edit/'.$page['id'], array('id' => 'page-form')) ?>
<div class="editor-layout">
	<div>
		<div class="card">
			<div class="field">
				<label for="title">Judul</label>
				<input type="text" id="title" name="title" class="title-input" required maxlength="500" data-slug-source value="<?= html_escape(isset($page['title']) ? $page['title'] : '') ?>">
			</div>
			<div class="field">
				<label for="slug">Slug URL</label>
				<input type="text" id="slug" name="slug" data-slug-target value="<?= html_escape(isset($page['slug']) ? $page['slug'] : '') ?>" placeholder="otomatis dari judul">
				<div class="permalink"><?= site_url('') ?><strong><?= html_escape(isset($page['slug']) ? $page['slug'] : '…') ?></strong></div>
				<?php if ($menu_usage): ?><div class="hint">Dipakai <?= $menu_usage ?> item menu; link menu ikut diperbarui jika slug diubah.</div><?php endif; ?>
			</div>
			<?php if ($blocks !== NULL): ?>
			<div class="field">
				<label>Isi halaman</label>
				<input type="hidden" name="editor_mode" value="blocks" data-editor-mode>
				<input type="hidden" name="content_hash" value="<?= $content_hash ?>">
				<div class="tabs editor-tabs" role="tablist">
					<a href="#" class="active" data-editor-tab="blocks" role="tab">Editor blok</a>
					<a href="#" data-editor-tab="raw" role="tab">HTML mentah</a>
				</div>
				<div data-editor-panel="blocks">
					<p class="hint">Halaman ini dibuat dengan Elementor. Ubah teks, judul, gambar, tab, dan item langsung di sini; struktur dan
						tampilannya tetap. Tombol &uarr; &darr; &#x29C9; &times; memindah, menduplikat, atau menghapus blok (perubahan lain ikut disimpan).</p>
					<div class="blk-editor"><?php $this->load->view('admin/pages/_blocks', array('blocks' => $blocks)); ?></div>
				</div>
				<div data-editor-panel="raw" hidden>
					<p class="hint">HTML mentah seluruh isi halaman (untuk pengguna yang paham struktur Elementor).</p>
					<textarea id="content" name="content" class="code" rows="24" data-shortcodes data-raw><?= html_escape($content) ?></textarea>
				</div>
			</div>
			<?php else: ?>
			<div class="field">
				<label for="content">Isi halaman</label>
				<?php if ($raw_editor): ?>
				<p class="hint">Halaman ini dibuat dengan Elementor, jadi diedit sebagai HTML mentah supaya struktur dan tampilannya tidak rusak.</p>
				<?php endif; ?>
				<textarea id="content" name="content" class="code" rows="24" data-shortcodes <?= $raw_editor ? 'data-raw' : '' ?>><?= html_escape($content) ?></textarea>
				<div class="hint">Tampil di bawah judul halaman, di antara hero dan tombol share; sidebar ditambahkan otomatis.</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="editor-side">
		<div class="card">
			<div class="field">
				<label for="status">Status</label>
				<select id="status" name="status">
					<option value="publish" <?= (isset($page['status']) && $page['status'] === 'publish') ? 'selected' : '' ?>>Terbit</option>
					<option value="draft" <?= ( ! isset($page['status']) OR $page['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
				</select>
			</div>
			<div class="field">
				<label for="published_at">Tanggal terbit</label>
				<input type="datetime-local" id="published_at" name="published_at" value="<?= $published_at ?>" step="1" required>
			</div>
			<div class="field">
				<label for="author_id">Author</label>
				<select id="author_id" name="author_id">
					<?php $selected_author = isset($page['author_id']) ? (int) $page['author_id'] : (int) $user['id']; ?>
					<?php foreach ($authors as $id => $name): ?>
					<option value="<?= $id ?>" <?= $selected_author === (int) $id ? 'selected' : '' ?>><?= html_escape($name) ?></option>
					<?php endforeach; ?>
				</select>
				<div class="hint">Tampil di bawah judul halaman.</div>
			</div>
			<button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Simpan</button>
			<?php if ( ! $is_new && $page['status'] === 'publish'): ?>
			<p style="text-align:center;margin:10px 0 0"><a href="<?= site_url($page['slug']) ?>" target="_blank" rel="noopener">Lihat halaman &nearr;</a></p>
			<?php endif; ?>
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
			<div class="hint">Hanya tampil di kartu hasil pencarian. Gambar hero halaman sama untuk semua halaman.</div>
		</div>
		<?php if ( ! $is_new): ?>
		<div class="card">
			<div class="hint" style="margin:0">Diubah: <?= date('d/m/Y H:i', strtotime($page['modified_at'])) ?></div>
		</div>
		<?php endif; ?>
	</div>
</div>
<?= form_close() ?>
<?php if ( ! $is_new): ?>
<?= form_open('admin/pages/delete/'.$page['id'], array('data-confirm' => 'Hapus halaman ini secara permanen?')) ?>
	<button type="submit" class="btn btn-danger">Hapus halaman</button>
<?= form_close() ?>
<?php endif; ?>
