<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kelola halaman statis (tabel pages, view NULL). Admin & editor.
 * Beranda tetap berupa file view (menu Beranda) dan tidak tampil di sini.
 */
class Pages extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('page_model', 'media_model'));
	}

	public function index()
	{
		$filter = array(
			'status' => in_array($this->input->get('status'), array('publish', 'draft'), TRUE) ? $this->input->get('status') : '',
			'q'      => trim((string) $this->input->get('q')),
		);
		$page = max(1, (int) $this->input->get('page'));
		list($pages, $total) = $this->page_model->admin_list($filter, $page);

		$this->view('admin/pages/index', array(
			'title'       => 'Halaman',
			'pages'       => $pages,
			'total'       => $total,
			'filter'      => $filter,
			'page'        => $page,
			'total_pages' => max(1, (int) ceil($total / Page_model::ADMIN_PER_PAGE)),
			'counts'      => $this->page_model->counts(),
		));
	}

	public function create()
	{
		$this->form(NULL);
	}

	public function edit($id = NULL)
	{
		$page = $this->page_model->find($id);
		if ( ! $page OR $page['view'] !== NULL)
		{
			show_404();
		}
		$this->form($page);
	}

	public function delete($id = NULL)
	{
		$this->require_post();
		$page = $this->page_model->find($id);
		if ( ! $page OR $page['view'] !== NULL)
		{
			show_404();
		}
		$used = $this->page_model->menu_usage($page);
		if ($used)
		{
			$this->flash('error', 'Halaman "'.$page['title'].'" masih dipakai '.$used.' item menu. Hapus atau ganti item menunya dulu.');
			redirect('admin/pages/edit/'.$page['id']);
		}
		$this->page_model->delete($page['id']);
		$this->flash('success', 'Halaman "'.$page['title'].'" dihapus.');
		redirect('admin/pages');
	}

	protected function form($page)
	{
		$errors = array();

		if ($this->input->method() === 'post')
		{
			list($data, $errors) = $this->validate($page);
			if (empty($errors))
			{
				$id = $this->page_model->save($page ? $page['id'] : NULL, $data);
				if ($id)
				{
					$this->flash('success', $this->op_message !== NULL ? $this->op_message : 'Halaman disimpan.');
					redirect('admin/pages/edit/'.$id.($this->op_anchor !== NULL ? '#'.$this->op_anchor : ''));
				}
				$errors[] = 'Gagal menyimpan ke database.';
			}
			// Editor blok memakai posisi byte konten tersimpan: saat ada error, tampilkan ulang dari konten tersimpan.
			$stored_content = ($page && $this->input->post('editor_mode') === 'blocks') ? $page['content'] : NULL;
			$page = array_merge($page ? $page : array(), $data);
			if ($stored_content !== NULL)
			{
				$page['content'] = $stored_content;
			}
		}

		$raw_editor = isset($page['content']) && Page_model::is_elementor($page['content']);

		$featured = ! empty($page['featured_media_id']) ? $this->media_model->find($page['featured_media_id']) : NULL;

		$this->view('admin/pages/form', $this->block_editor_data(isset($page['content']) ? $page['content'] : '', ! empty($page['id'])) + array(
			'title'      => ! empty($page['id']) ? 'Edit halaman' : 'Halaman baru',
			'page'       => $page,
			'errors'     => $errors,
			'content'    => isset($page['content']) ? wp_content($page['content']) : '',
			'featured'   => $featured,
			'authors'    => $this->author_model->options(),
			'menu_usage' => ! empty($page['id']) ? $this->page_model->menu_usage($page) : 0,
			'raw_editor' => $raw_editor,
			'scripts'    => $this->load->view('admin/posts/_editor_js', array(), TRUE),
		));
	}

	/**
	 * Mengembalikan [data kolom pages, error].
	 */
	protected function validate($page)
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
		$except = $page ? $page['id'] : NULL;
		if ($slug === '')
		{
			$errors[] = 'Slug tidak valid.';
		}
		elseif (root_slug_conflict($slug, 'posts'))
		{
			$errors[] = 'Slug "'.$slug.'" sudah dipakai post atau URL lain di situs.';
		}
		else
		{
			$slug = unique_slug($slug, function ($s) use ($except) {
				return $this->page_model->slug_taken($s, $except);
			});
		}

		$content = content_to_tokens((string) $in->post('content'));
		if ($page && $in->post('editor_mode') === 'blocks' && Page_model::is_elementor($page['content']))
		{
			// Editor blok (konten Elementor): hanya field yang berubah + satu operasi blok yang diterapkan.
			list($content, $block_errors) = $this->apply_blocks($page['content']);
			$errors = array_merge($errors, $block_errors);
		}
		// Isi yang tidak diubah disimpan apa adanya (termasuk whitespace hasil impor WordPress).
		elseif ($page && isset($page['content']) && str_replace("\r\n", "\n", (string) $in->post('content')) === str_replace("\r\n", "\n", wp_content($page['content'])))
		{
			$content = $page['content'];
		}
		elseif ( ! Page_model::is_elementor($content))
		{
			// Seperti the_content() + template Blocksy: isi diakhiri baris baru, lalu "\t\t</div>".
			$content = rtrim(str_replace("\r\n", "\n", $content))."\n\t\t";
		}

		$published = DateTime::createFromFormat('!Y-m-d\TH:i:s', (string) $in->post('published_at'))
			?: DateTime::createFromFormat('!Y-m-d\TH:i', (string) $in->post('published_at'));
		if ( ! $published)
		{
			$errors[] = 'Tanggal terbit tidak valid.';
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

		list($head, $foot) = $this->page_model->layout_for($content, $page ? $page['layout_head'] : NULL, $page ? $page['layout_foot'] : NULL);

		$data = array(
			'title'             => $title,
			'slug'              => $slug,
			'content'           => $content,
			'author_id'         => $author_id,
			'featured_media_id' => $featured ? $featured : NULL,
			'status'            => ($in->post('status') === 'publish') ? 'publish' : 'draft',
			'template'          => ($in->post('template') === 'full-width') ? 'full-width' : 'default',
			'layout_head'       => $head,
			'layout_foot'       => $foot,
			'published_at'      => $published ? $published->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
			'modified_at'       => date('Y-m-d H:i:s'),
		);

		return array($data, $errors);
	}
}
