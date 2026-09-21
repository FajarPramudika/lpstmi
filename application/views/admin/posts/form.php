<?php defined('BASEPATH') OR exit('No direct script access allowed');
$is_new = empty($post['id']);
$published_at = ! empty($post['published_at']) ? date('Y-m-d\TH:i:s', strtotime($post['published_at'])) : date('Y-m-d\TH:i:s');
?>
<?= form_open($is_new ? 'admin/posts/create' : 'admin/posts/edit/'.$post['id'], array('id' => 'post-form')) ?>
<div class="editor-layout">
	<div>
		<div class="card">
			<div class="field">
				<label for="title">Judul</label>
				<input type="text" id="title" name="title" class="title-input" required maxlength="500" data-slug-source value="<?= html_escape(isset($post['title']) ? $post['title'] : '') ?>">
			</div>
			<div class="field">
				<label for="slug">Slug URL</label>
				<input type="text" id="slug" name="slug" data-slug-target value="<?= html_escape(isset($post['slug']) ? $post['slug'] : '') ?>" placeholder="otomatis dari judul">
				<div class="permalink"><?= site_url('') ?><strong><?= html_escape(isset($post['slug']) ? $post['slug'] : '…') ?></strong></div>
			</div>
			<?php if ($blocks !== NULL): ?>
			<?php $this->load->view('admin/_block_editor', array('label' => 'Konten')); ?>
			<?php else: ?>
			<div class="field">
				<label for="content">Konten</label>
				<?php if ($raw_editor): ?>
				<p class="hint">Post ini dibuat dengan Elementor, jadi diedit sebagai HTML mentah supaya struktur dan tampilannya tidak rusak.</p>
				<?php endif; ?>
				<textarea id="content" name="content" class="code" rows="24" data-shortcodes <?= $raw_editor ? 'data-raw' : '' ?>><?= html_escape($content) ?></textarea>
			</div>
			<?php endif; ?>
			<div class="field">
				<label for="excerpt">Ringkasan</label>
				<textarea id="excerpt" name="excerpt" rows="3"><?= html_escape($excerpt_text) ?></textarea>
				<div class="hint">Tampil di kartu halaman kategori/tag. Kosongkan untuk dibuat otomatis (40 kata pertama konten).</div>
			</div>
		</div>
	</div>
	<div class="editor-side">
		<div class="card">
			<div class="field">
				<label for="status">Status</label>
				<select id="status" name="status">
					<option value="publish" <?= (isset($post['status']) && $post['status'] === 'publish') ? 'selected' : '' ?>>Terbit</option>
					<option value="draft" <?= ( ! isset($post['status']) OR $post['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
				</select>
			</div>
			<div class="field">
				<label for="published_at">Tanggal terbit</label>
				<input type="datetime-local" id="published_at" name="published_at" value="<?= $published_at ?>" step="1" required>
			</div>
			<div class="field">
				<label for="author_id">Author</label>
				<select id="author_id" name="author_id">
					<?php $selected_author = isset($post['author_id']) ? (int) $post['author_id'] : (int) $user['id']; ?>
					<?php foreach ($authors as $id => $name): ?>
					<option value="<?= $id ?>" <?= $selected_author === (int) $id ? 'selected' : '' ?>><?= html_escape($name) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Simpan</button>
			<p style="text-align:center;margin:10px 0 0"><a class="btn btn-sm" href="<?= site_url('admin/posts') ?>">&larr; Kembali ke Post</a></p>
			<?php if ( ! $is_new && $post['status'] === 'publish'): ?>
			<p style="text-align:center;margin:10px 0 0"><a href="<?= site_url($post['slug']) ?>" target="_blank" rel="noopener">Lihat post &nearr;</a></p>
			<?php endif; ?>
		</div>
		<div class="card">
			<label>Kategori</label>
			<div class="checklist">
				<?php foreach ($categories as $c): ?>
				<label><input type="checkbox" name="categories[]" value="<?= $c['id'] ?>" <?= in_array((int) $c['id'], $category_ids, TRUE) ? 'checked' : '' ?>> <?= html_escape($c['name']) ?></label>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="card">
			<div class="field" style="margin:0">
				<label for="tags">Tag</label>
				<input type="text" id="tags" name="tags" value="<?= html_escape(implode(', ', $tag_names)) ?>" list="tag-list">
				<div class="hint">Pisahkan dengan koma. Tag baru dibuat otomatis.</div>
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
			<div class="hint">Tampil di kartu arsip dan navigasi post sebelumnya/berikutnya.</div>
		</div>
		<?php if ( ! $is_new): ?>
		<div class="card">
			<div class="hint" style="margin:0 0 10px">Diubah: <?= date('d/m/Y H:i', strtotime($post['modified_at'])) ?></div>
		</div>
		<?php endif; ?>
	</div>
</div>
<?= form_close() ?>
<?php if ( ! $is_new): ?>
<?= form_open('admin/posts/delete/'.$post['id'], array('data-confirm' => 'Hapus post ini secara permanen?')) ?>
	<button type="submit" class="btn btn-danger">Hapus post</button>
<?= form_close() ?>
<?php endif; ?>
