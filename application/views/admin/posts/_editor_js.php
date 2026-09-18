<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script src="<?= base_url('assets/admin/vendor/tinymce/tinymce.min.js') ?>"></script>
<script>
(function () {
	var textarea = document.getElementById('content');
	if (!textarea || textarea.hasAttribute('data-raw')) { return; }

	tinymce.init({
		target: textarea,
		height: 560,
		menubar: 'edit insert format table view',
		plugins: 'lists link image table code autolink media wordcount fullscreen',
		toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image mediapicker table | removeformat code fullscreen',
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
	});
})();
</script>
