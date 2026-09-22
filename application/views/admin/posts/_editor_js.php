<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script src="<?= base_url('assets/admin/vendor/tinymce/tinymce.min.js') ?>"></script>
<div class="modal" id="download-modal" role="dialog" aria-modal="true" aria-label="Pilih paket download">
	<div class="modal-box" style="width:min(640px,100%)">
		<div class="modal-head">
			<strong>Sisipkan kartu download</strong>
			<input type="search" placeholder="Cari judul paket…" data-search style="width:220px">
		</div>
		<div class="modal-body"><div class="pick-list" data-list></div></div>
		<div class="modal-foot">
			<div class="pager" style="margin:0"><button type="button" class="btn btn-sm" data-prev>&larr;</button><span data-page-info></span><button type="button" class="btn btn-sm" data-next>&rarr;</button></div>
			<button type="button" class="btn" data-close>Batal</button>
		</div>
	</div>
</div>
<script>
(function () {
	var textarea = document.getElementById('content');
	if (!textarea) { return; }
	var shortcodes = textarea.hasAttribute('data-shortcodes');

	// Pemilih paket Download Manager -> [wpdm_package id='N'] (kartu dirender dari tabel downloads).
	var modal = document.getElementById('download-modal');
	var list = modal.querySelector('[data-list]');
	var state = { page: 1, total: 1, q: '', onSelect: null };
	function load() {
		var url = <?= json_encode(site_url('admin/downloads/browse')) ?> + '?page=' + state.page + '&q=' + encodeURIComponent(state.q);
		list.innerHTML = '<p class="muted">Memuat…</p>';
		fetch(url, { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (json) {
			state.total = json.total_pages;
			modal.querySelector('[data-page-info]').textContent = json.page + ' / ' + json.total_pages;
			list.innerHTML = json.items.length ? '' : '<p class="muted">Tidak ada paket.</p>';
			json.items.forEach(function (item) {
				var b = document.createElement('button');
				b.type = 'button';
				b.className = 'pick-item';
				b.innerHTML = '<span></span><small class="muted"></small>';
				b.firstChild.textContent = item.title;
				b.lastChild.textContent = (item.size || '') + (item.status === 'draft' ? ' · draft (tidak tampil)' : '') + ' · ID ' + item.id;
				b.addEventListener('click', function () {
					modal.classList.remove('open');
					state.onSelect("[wpdm_package id='" + item.id + "']");
				});
				list.appendChild(b);
			});
		});
	}
	window.openDownloadPicker = function (onSelect) {
		state.onSelect = onSelect;
		modal.classList.add('open');
		load();
	};
	modal.querySelector('[data-close]').addEventListener('click', function () { modal.classList.remove('open'); });
	modal.querySelector('[data-prev]').addEventListener('click', function () { if (state.page > 1) { state.page--; load(); } });
	modal.querySelector('[data-next]').addEventListener('click', function () { if (state.page < state.total) { state.page++; load(); } });
	modal.querySelector('[data-search]').addEventListener('input', function (e) { state.q = e.target.value; state.page = 1; load(); });
	modal.addEventListener('click', function (e) { if (e.target === modal) { modal.classList.remove('open'); } });

	// Sisipkan teks di posisi kursor textarea biasa.
	function insertAtCursor(el, code) {
		var start = el.selectionStart, end = el.selectionEnd;
		el.value = el.value.slice(0, start) + code + el.value.slice(end);
		el.focus();
		el.selectionStart = el.selectionEnd = start + code.length;
	}

	// Konfigurasi TinyMCE bersama (editor utama & field teks di editor blok).
	function richConfig(target, height, withShortcodes) {
		return {
			target: target,
			height: height,
			menubar: 'edit insert format table view',
			plugins: 'lists link image table code autolink media wordcount fullscreen',
			toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image mediapicker' + (withShortcodes ? ' downloadpicker' : '') + ' table | removeformat code fullscreen',
			// Semua tombol terlihat (Media & Download tidak terlipat ke menu "…").
			toolbar_mode: 'wrap',
			block_formats: 'Paragraf=p; Judul 2=h2; Judul 3=h3; Judul 4=h4',
			// Pertahankan HTML apa adanya (konten hasil impor WordPress).
			verify_html: false,
			valid_elements: '*[*]',
			extended_valid_elements: '*[*]',
			valid_children: '+body[style]',
			entity_encoding: 'raw',
			// URL gambar/link tetap absolut; disimpan sebagai token {base_url} di server.
			relative_urls: false,
			remove_script_host: false,
			convert_urls: false,
			image_caption: true,
			automatic_uploads: true,
			images_file_types: 'jpg,jpeg,png,gif,webp',
			images_upload_handler: function (blobInfo) {
				var file = new File([blobInfo.blob()], blobInfo.filename(), { type: blobInfo.blob().type });
				return window.adminUpload(file).then(function (json) { return json.location; });
			},
			content_style: 'body{font-family:Rubik,-apple-system,Segoe UI,Roboto,sans-serif;font-size:16px;line-height:1.65;max-width:860px;margin:16px auto;padding:0 12px} img{max-width:100%;height:auto}',
			setup: function (editor) {
				editor.ui.registry.addButton('downloadpicker', {
					text: 'Download',
					tooltip: 'Sisipkan kartu paket download',
					onAction: function () {
						window.openDownloadPicker(function (code) { editor.insertContent(code); });
					}
				});
				editor.ui.registry.addButton('mediapicker', {
					text: 'Media',
					tooltip: 'Sisipkan dari pustaka media',
					onAction: function () {
						window.openMediaPicker({ type: 'all', onSelect: function (item) {
							if (item.is_image) {
								editor.insertContent('<img src="' + item.url + '" alt="' + (item.alt || '').replace(/"/g, '&quot;') + '" width="' + item.width + '" height="' + item.height + '">');
							} else {
								editor.insertContent('<a href="' + item.url + '" target="_blank" rel="noopener">' + item.name + '</a>');
							}
						} });
					}
				});
			}
		};
	}

	// Editor blok Elementor (form halaman).
	var modeInput = document.querySelector('[data-editor-mode]');
	if (modeInput) {
		document.querySelectorAll('[data-editor-tab]').forEach(function (tab) {
			tab.addEventListener('click', function (e) {
				e.preventDefault();
				var mode = tab.getAttribute('data-editor-tab');
				modeInput.value = mode;
				document.querySelectorAll('[data-editor-tab]').forEach(function (t) { t.classList.toggle('active', t === tab); });
				document.querySelectorAll('[data-editor-panel]').forEach(function (p) { p.hidden = p.getAttribute('data-editor-panel') !== mode; });
			});
		});
		document.querySelectorAll('[data-rich-open]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var ta = btn.parentNode.querySelector('textarea');
				btn.hidden = true;
				tinymce.init(richConfig(ta, 360, true));
			});
		});
		document.querySelectorAll('[data-download-insert]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var ta = btn.parentNode.querySelector('textarea');
				window.openDownloadPicker(function (code) {
					var ed = tinymce.get(ta.id);
					if (ed) { ed.insertContent(code); } else { insertAtCursor(ta, code); }
				});
			});
		});
		document.querySelectorAll('[data-block-image]').forEach(function (box) {
			box.querySelector('[data-image-pick]').addEventListener('click', function () {
				window.openMediaPicker({ type: 'image', onSelect: function (item) {
					box.querySelector('input[type=hidden]').value = item.id;
					box.querySelector('img').src = item.url;
				} });
			});
		});
		document.querySelectorAll('[data-confirm-click]').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				if (btn.getAttribute('data-confirmed') === '1') {
					btn.removeAttribute('data-confirmed');
					return;
				}
				e.preventDefault();
				window.openDeleteConfirm(btn.getAttribute('data-confirm-click'), function () {
					btn.setAttribute('data-confirmed', '1');
					btn.click();
				}, btn);
			});
		});
		// Textarea blok perlu id agar bisa dipakai TinyMCE; tingginya mengikuti isi (maks. 360px).
		document.querySelectorAll('.blk-editor textarea').forEach(function (ta, i) {
			if (ta.classList.contains('blk-rich') && !ta.id) { ta.id = 'blk-rich-' + i; }
			ta.style.height = 'auto';
			ta.style.height = Math.min(360, ta.scrollHeight + 4) + 'px';
		});
	}

	if (textarea.hasAttribute('data-raw')) {
		// HTML mentah (Elementor): sisipkan kode pendek di posisi kursor.
		if (shortcodes) {
			var btn = document.createElement('button');
			btn.type = 'button';
			btn.className = 'btn btn-sm';
			btn.style.marginBottom = '8px';
			btn.textContent = 'Sisipkan kartu download';
			btn.addEventListener('click', function () {
				window.openDownloadPicker(function (code) { insertAtCursor(textarea, code); });
			});
			textarea.parentNode.insertBefore(btn, textarea);
		}
		return;
	}

	tinymce.init(richConfig(textarea, 560, shortcodes));
})();
</script>
