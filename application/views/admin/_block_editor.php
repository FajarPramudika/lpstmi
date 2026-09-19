<?php defined('BASEPATH') OR exit('No direct script access allowed');
/** Editor blok + tab HTML mentah untuk konten Elementor ($blocks, $content_hash, $content dari controller). */
?>
			<div class="field">
				<label><?= html_escape($label) ?></label>
				<input type="hidden" name="editor_mode" value="blocks" data-editor-mode>
				<input type="hidden" name="content_hash" value="<?= $content_hash ?>">
				<div class="tabs editor-tabs" role="tablist">
					<a href="#" class="active" data-editor-tab="blocks" role="tab">Editor blok</a>
					<a href="#" data-editor-tab="raw" role="tab">HTML mentah</a>
				</div>
				<div data-editor-panel="blocks">
					<p class="hint">Isi ini dibuat dengan Elementor. Ubah teks, judul, gambar, tab, dan item langsung di sini; struktur dan
						tampilannya tetap. Tombol &uarr; &darr; &#x29C9; &times; memindah, menduplikat, atau menghapus blok (perubahan lain ikut disimpan).</p>
					<div class="blk-editor"><?php $this->load->view('admin/_blocks', array('blocks' => $blocks)); ?></div>
				</div>
				<div data-editor-panel="raw" hidden>
					<p class="hint">HTML mentah seluruh isi (untuk pengguna yang paham struktur Elementor).</p>
					<textarea id="content" name="content" class="code" rows="24" data-shortcodes data-raw><?= html_escape($content) ?></textarea>
				</div>
			</div>
