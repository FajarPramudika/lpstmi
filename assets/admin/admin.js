/* Panel admin STMI: konfirmasi hapus, slug otomatis, pemilih media, menu mobile. */
(function () {
	'use strict';

	var csrfName = document.querySelector('meta[name="csrf-name"]');
	var csrfHash = document.querySelector('meta[name="csrf-hash"]');
	var baseUrl = document.querySelector('meta[name="admin-base"]').content;

	function csrf(form) {
		if (csrfName && csrfHash) {
			form.append(csrfName.content, csrfHash.content);
		}
		return form;
	}

	// Konfirmasi sebelum aksi berbahaya.
	document.addEventListener('submit', function (e) {
		var msg = e.target.getAttribute('data-confirm');
		if (msg && !window.confirm(msg)) {
			e.preventDefault();
		}
	});

	// Menu samping di layar kecil.
	var toggle = document.querySelector('.menu-toggle');
	if (toggle) {
		toggle.addEventListener('click', function () {
			document.querySelector('.sidebar').classList.toggle('open');
		});
	}

	// Slug otomatis dari judul (hanya selama slug belum diubah manual).
	var title = document.querySelector('[data-slug-source]');
	var slug = document.querySelector('[data-slug-target]');
	if (title && slug) {
		var manual = slug.value !== '';
		slug.addEventListener('input', function () { manual = slug.value !== ''; });
		title.addEventListener('input', function () {
			if (manual) { return; }
			slug.placeholder = title.value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '')
				.replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
		});
	}

	// Upload lewat AJAX (dipakai editor & pemilih media).
	window.adminUpload = function (file) {
		var data = csrf(new FormData());
		data.append('file', file);
		return fetch(baseUrl + 'admin/media/upload?json=1', {
			method: 'POST', body: data, credentials: 'same-origin',
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		}).then(function (r) {
			return r.json().then(function (json) {
				if (!r.ok || !json.ok) { throw new Error(json.error || 'Upload gagal'); }
				return json;
			});
		});
	};

	// Pemilih media (modal).
	var modal = document.getElementById('media-modal');
	if (!modal) { return; }
	var grid = modal.querySelector('.media-grid');
	var pageInfo = modal.querySelector('[data-page-info]');
	var state = { page: 1, total: 1, type: 'image', q: '', selected: null, onSelect: null };

	function load() {
		var url = baseUrl + 'admin/media/browse?page=' + state.page + '&type=' + state.type + '&q=' + encodeURIComponent(state.q);
		grid.innerHTML = '<p class="muted">Memuat…</p>';
		fetch(url, { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (json) {
			state.total = json.total_pages;
			pageInfo.textContent = 'Halaman ' + json.page + ' dari ' + json.total_pages;
			grid.innerHTML = '';
			if (!json.items.length) { grid.innerHTML = '<p class="muted">Belum ada media.</p>'; }
			json.items.forEach(function (item) {
				var el = document.createElement('div');
				el.className = 'media-item';
				el.innerHTML = '<div class="media-thumb">' + (item.is_image
					? '<img loading="lazy" alt="">' : '<span class="ext"></span>') + '</div><div class="media-meta"></div>';
				if (item.is_image) { el.querySelector('img').src = item.thumb; }
				else { el.querySelector('.ext').textContent = item.name.split('.').pop(); }
				el.querySelector('.media-meta').textContent = item.name;
				el.addEventListener('click', function () {
					grid.querySelectorAll('.selected').forEach(function (s) { s.classList.remove('selected'); });
					el.classList.add('selected');
					state.selected = item;
				});
				el.addEventListener('dblclick', choose);
				grid.appendChild(el);
			});
		});
	}

	function choose() {
		if (state.selected && state.onSelect) { state.onSelect(state.selected); }
		close();
	}

	function close() { modal.classList.remove('open'); }

	window.openMediaPicker = function (options) {
		state.type = options.type || 'image';
		state.onSelect = options.onSelect;
		state.selected = null;
		state.page = 1;
		modal.classList.add('open');
		load();
	};

	modal.querySelector('[data-close]').addEventListener('click', close);
	modal.querySelector('[data-choose]').addEventListener('click', choose);
	modal.querySelector('[data-prev]').addEventListener('click', function () { if (state.page > 1) { state.page--; load(); } });
	modal.querySelector('[data-next]').addEventListener('click', function () { if (state.page < state.total) { state.page++; load(); } });
	modal.querySelector('[data-search]').addEventListener('change', function (e) { state.q = e.target.value; state.page = 1; load(); });
	modal.querySelector('[data-upload]').addEventListener('change', function (e) {
		var files = Array.prototype.slice.call(e.target.files);
		Promise.all(files.map(window.adminUpload)).then(function () { state.page = 1; load(); })
			.catch(function (err) { window.alert(err.message); });
		e.target.value = '';
	});
	modal.addEventListener('click', function (e) { if (e.target === modal) { close(); } });

	// Gambar unggulan di form post.
	var featured = document.querySelector('[data-featured]');
	if (featured) {
		var input = featured.querySelector('input[type=hidden]');
		var preview = featured.querySelector('.featured-preview');
		var remove = featured.querySelector('[data-featured-remove]');
		featured.querySelector('[data-featured-pick]').addEventListener('click', function () {
			window.openMediaPicker({ type: 'image', onSelect: function (item) {
				input.value = item.id;
				preview.innerHTML = '';
				var img = document.createElement('img');
				img.src = item.thumb;
				img.alt = '';
				preview.appendChild(img);
				remove.hidden = false;
			} });
		});
		remove.addEventListener('click', function () {
			input.value = '';
			preview.innerHTML = '<span class="muted">Belum ada gambar</span>';
			remove.hidden = true;
		});
	}
})();
