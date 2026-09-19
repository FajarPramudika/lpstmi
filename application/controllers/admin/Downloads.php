<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kelola paket Download Manager (/download/<slug>). Admin & editor.
 */
class Downloads extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('download_model', 'media_model'));
	}

	public function index()
	{
		$filter = array(
			'status' => in_array($this->input->get('status'), array('publish', 'draft'), TRUE) ? $this->input->get('status') : '',
			'q'      => trim((string) $this->input->get('q')),
		);
		$page = max(1, (int) $this->input->get('page'));
		list($items, $total) = $this->download_model->admin_list($filter, $page);

		$this->view('admin/downloads/index', array(
			'title'       => 'Download',
			'items'       => $items,
			'total'       => $total,
			'filter'      => $filter,
			'page'        => $page,
			'total_pages' => max(1, (int) ceil($total / Download_model::ADMIN_PER_PAGE)),
			'counts'      => $this->download_model->counts(),
		));
	}

	public function create()
	{
		$this->form(NULL);
	}

	public function edit($id = NULL)
	{
		$download = $this->download_model->find($id);
		if ( ! $download)
		{
			show_404();
		}
		$this->form($download);
	}

	public function delete($id = NULL)
	{
		$this->require_post();
		$download = $this->download_model->find($id);
		if ( ! $download)
		{
			show_404();
		}
		$this->download_model->delete($download['id']);
		$this->flash('success', 'Paket "'.$download['title'].'" dihapus. File di pustaka media tidak ikut terhapus.');
		redirect('admin/downloads');
	}

	protected function form($download)
	{
		$errors = array();

		if ($this->input->method() === 'post')
		{
			list($data, $errors) = $this->validate($download);
			if (empty($errors))
			{
				$id = $this->download_model->save($download ? $download['id'] : NULL, $data);
				$this->flash('success', 'Paket download disimpan.');
				redirect('admin/downloads/edit/'.$id);
			}
			$download = array_merge($download ? $download : array(), $data);
		}

		$featured = ! empty($download['featured_media_id']) ? $this->media_model->find($download['featured_media_id']) : NULL;
		$file_input = isset($download['file']) ? $download['file'] : '';
		if ($file_input !== '' && ! Download_model::is_external($download))
		{
			$file_input = base_url('wp-content/uploads/'.$file_input);
		}

		$this->view('admin/downloads/form', array(
			'title'       => ! empty($download['id']) ? 'Edit paket download' : 'Paket download baru',
			'download'    => $download,
			'errors'      => $errors,
			'description' => isset($download['description']) ? wp_content($download['description']) : '',
			'file_input'  => $file_input,
			'featured'    => $featured,
			'authors'     => $this->author_model->options(),
			'scripts'     => $this->load->view('admin/posts/_editor_js', array(), TRUE),
		));
	}

	/**
	 * Mengembalikan [data kolom downloads, error].
	 */
	protected function validate($download)
	{
		$in = $this->input;
		$errors = array();

		$title = trim((string) $in->post('title'));
		if ($title === '')
		{
			$errors[] = 'Judul wajib diisi.';
		}
		elseif (mb_strlen($title) > 500)
		{
			$errors[] = 'Judul maksimal 500 karakter.';
		}

		$slug = slugify(trim((string) $in->post('slug')) !== '' ? $in->post('slug') : $title);
		$except = $download ? $download['id'] : NULL;
		if ($slug === '')
		{
			$errors[] = 'Slug tidak valid.';
		}
		else
		{
			$slug = unique_slug($slug, function ($s) use ($except) {
				return $this->download_model->slug_taken($s, $except);
			});
		}

		// File: URL file di situs ini -> path relatif wp-content/uploads/; selain itu harus URL http(s) lengkap.
		$file = trim((string) $in->post('file'));
		$uploads = base_url('wp-content/uploads/');
		if (strpos($file, $uploads) === 0)
		{
			$file = rawurldecode(substr($file, strlen($uploads)));
		}
		elseif (strpos($file, '/wp-content/uploads/') === 0)
		{
			$file = rawurldecode(substr($file, strlen('/wp-content/uploads/')));
		}

		$external = (bool) preg_match('#^https?://\S+$#i', $file);
		$local = $external ? NULL : Download_model::local_path(array('file' => $file));
		if ($file === '')
		{
			$errors[] = 'Pilih file dari pustaka media, atau isi URL file di situs lain.';
		}
		elseif ( ! $external && $local === NULL)
		{
			$errors[] = 'File tidak ditemukan di folder uploads. Pilih lewat tombol "Pilih file".';
		}
		elseif (mb_strlen($file) > 500)
		{
			$errors[] = 'Path/URL file maksimal 500 karakter.';
		}

		// Ukuran dihitung ulang hanya jika file berubah (label hasil impor WordPress dipertahankan).
		if ($download && isset($download['file']) && $download['file'] === $file)
		{
			$size = $download['file_size'];
		}
		else
		{
			$size = $local ? Download_model::size_label(filesize($local)) : '';
		}

		$published_raw = (string) $in->post('published_at');
		$published = DateTime::createFromFormat('!Y-m-d\TH:i:s', $published_raw) ?: DateTime::createFromFormat('!Y-m-d\TH:i', $published_raw);
		if ( ! $published)
		{
			$errors[] = 'Tanggal dibuat tidak valid.';
		}

		$author_id = (int) $in->post('author_id');
		if ( ! $this->author_model->find($author_id))
		{
			$errors[] = 'Author tidak ditemukan.';
		}

		$featured = (int) $in->post('featured_media_id');
		if ($featured && ! $this->media_model->find($featured))
		{
			$featured = 0;
		}

		$description = content_to_tokens((string) $in->post('description'));
		// Paket baru berisi PDF lokal tanpa deskripsi: tampilkan PDF-nya seperti paket-paket lama (PDF Embedder).
		if ( ! $download && trim(strip_tags($description)) === '' && $local && preg_match('/\.pdf$/i', $file))
		{
			$name = pathinfo($file, PATHINFO_FILENAME);
			$description = '<p><a href="{base_url}wp-content/uploads/'.html_escape($file).'" class="pdfemb-viewer" style="" data-width="max" data-height="max" data-toolbar="both" data-toolbar-fixed="on">'.html_escape($name).'</a></p>'."\n";
		}

		$button = trim((string) $in->post('button_label'));

		$data = array(
			'title'             => $title,
			'slug'              => $slug,
			'description'       => $description,
			'template'          => ($in->post('template') === 'default') ? 'default' : 'simplified',
			'button_label'      => ($button === '' OR $button === 'Download') ? NULL : mb_substr($button, 0, 255),
			'file'              => $file,
			'file_size'         => $size,
			'author_id'         => $author_id,
			'featured_media_id' => $featured ? $featured : NULL,
			'status'            => ($in->post('status') === 'publish') ? 'publish' : 'draft',
			'published_at'      => $published ? $published->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
			'modified_at'       => date('Y-m-d H:i:s'),
		);
		if ( ! $download)
		{
			$data['download_count'] = 0;
		}

		return array($data, $errors);
	}
}
