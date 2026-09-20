<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kelola post (berita, pengumuman, artikel).
 */
class Posts extends Admin_Controller {

	/** Editor ikut mengelola konten di sini. */
	protected $roles = array('admin', 'editor');

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('post_model', 'term_model', 'media_model'));
	}

	public function index()
	{
		$filter = array(
			'status'   => in_array($this->input->get('status'), array('publish', 'draft'), TRUE) ? $this->input->get('status') : '',
			'category' => (int) $this->input->get('category'),
			'q'        => trim((string) $this->input->get('q')),
		);
		$page = max(1, (int) $this->input->get('page'));
		list($posts, $total) = $this->post_model->admin_list($filter, $page);

		$this->view('admin/posts/index', array(
			'title'       => 'Post',
			'posts'       => $posts,
			'total'       => $total,
			'filter'      => $filter,
			'page'        => $page,
			'total_pages' => max(1, (int) ceil($total / Post_model::ADMIN_PER_PAGE)),
			'categories'  => $this->term_model->all('category'),
			'counts'      => $this->post_model->counts(),
		));
	}

	public function create()
	{
		$this->form(NULL);
	}

	public function edit($id = NULL)
	{
		$post = $this->post_model->find($id);
		if ( ! $post)
		{
			show_404();
		}
		$this->form($post);
	}

	public function delete($id = NULL)
	{
		$this->require_post();
		$post = $this->post_model->find($id);
		if ( ! $post)
		{
			show_404();
		}
		$this->post_model->delete($post['id']);
		$this->flash('success', 'Post "'.$post['title'].'" dihapus.');
		redirect('admin/posts');
	}

	protected function form($post)
	{
		$terms = $post ? $this->post_model->term_ids($post['id']) : array('category' => array(), 'post_tag' => array());
		$errors = array();

		if ($this->input->method() === 'post')
		{
			list($data, $category_ids, $tag_names, $errors) = $this->validate($post);

			if (empty($errors))
			{
				$tag_ids = $this->term_model->tag_ids($tag_names);
				$id = $this->post_model->save($post ? $post['id'] : NULL, $data, $category_ids, $tag_ids);
				if ($id)
				{
					$this->flash('success', $this->op_message !== NULL ? $this->op_message : 'Post disimpan.');
					redirect('admin/posts/edit/'.$id.($this->op_anchor !== NULL ? '#'.$this->op_anchor : ''));
				}
				$errors[] = 'Gagal menyimpan ke database.';
			}

			// Isi ulang form dengan input yang dikirim. Editor blok memakai posisi byte konten tersimpan.
			$stored_content = ($post && $this->input->post('editor_mode') === 'blocks') ? $post['content'] : NULL;
			$post = array_merge($post ? $post : array(), $data, array('id' => $post ? $post['id'] : NULL));
			if ($stored_content !== NULL)
			{
				$post['content'] = $stored_content;
			}
			$terms = array(
				'category' => array_map(function ($id) { return array('id' => $id); }, $category_ids),
				'post_tag' => array_map(function ($n) { return array('name' => $n); }, $tag_names),
			);
		}

		$featured = ( ! empty($post['featured_media_id'])) ? $this->media_model->find($post['featured_media_id']) : NULL;

		$this->view('admin/posts/form', $this->block_editor_data($post ? $post['content'] : '', $post && ! empty($post['id'])) + array(
			'title'        => $post && ! empty($post['id']) ? 'Edit post' : 'Post baru',
			'post'         => $post,
			'errors'       => $errors,
			'content'      => $post ? wp_content($post['content']) : '',
			'excerpt_text' => $post ? $this->excerpt_text($post) : '',
			'category_ids' => array_map('intval', array_column($terms['category'], 'id')),
			'tag_names'    => array_column($terms['post_tag'], 'name'),
			'categories'   => $this->term_model->all('category'),
			'authors'      => $this->author_model->options(),
			'featured'     => $featured,
			'raw_editor'   => $post && strpos((string) $post['content'], 'data-elementor-type=') !== FALSE,
			'scripts'      => $this->load->view('admin/posts/_editor_js', array(), TRUE),
		));
	}

	/**
	 * Mengembalikan [data kolom posts, id kategori, nama tag, error].
	 */
	protected function validate($post)
	{
		$in = $this->input;
		$errors = array();

		$title = trim((string) $in->post('title'));
		$content = content_to_tokens((string) $in->post('content'));
		if ($post && $in->post('editor_mode') === 'blocks' && strpos($post['content'], 'data-elementor-type=') !== FALSE)
		{
			// Editor blok (post Elementor): hanya field yang berubah + satu operasi blok yang diterapkan.
			list($content, $block_errors) = $this->apply_blocks($post['content']);
			$errors = array_merge($errors, $block_errors);
		}
		$status = ($in->post('status') === 'publish') ? 'publish' : 'draft';

		if ($title === '')
		{
			$errors[] = 'Judul wajib diisi.';
		}
		elseif (mb_strlen($title) > 500)
		{
			$errors[] = 'Judul maksimal 500 karakter.';
		}

		$slug = slugify(trim((string) $in->post('slug')) !== '' ? $in->post('slug') : $title);
		$except = $post ? $post['id'] : NULL;
		if ($slug === '')
		{
			$errors[] = 'Slug tidak valid.';
		}
		elseif (root_slug_conflict($slug, 'pages'))
		{
			$errors[] = 'Slug "'.$slug.'" sudah dipakai halaman lain di situs.';
		}
		else
		{
			$slug = unique_slug($slug, function ($s) use ($except) {
				return $this->post_model->slug_taken($s, $except);
			});
		}

		// Browser mengirim detik hanya jika tidak nol (step="1").
		$raw_date = (string) $in->post('published_at');
		$published_at = DateTime::createFromFormat('!Y-m-d\TH:i:s', $raw_date) ?: DateTime::createFromFormat('!Y-m-d\TH:i', $raw_date);
		if ( ! $published_at)
		{
			$errors[] = 'Tanggal terbit tidak valid.';
		}

		$author_id = (int) $in->post('author_id');
		if ( ! $this->author_model->find($author_id))
		{
			$errors[] = 'Author tidak ditemukan.';
		}

		$category_ids = array_values(array_unique(array_map('intval', (array) $in->post('categories'))));
		$valid_categories = array_map('intval', array_column($this->term_model->all('category'), 'id'));
		$category_ids = array_values(array_intersect($category_ids, $valid_categories));
		if (empty($category_ids))
		{
			$errors[] = 'Pilih minimal satu kategori.';
		}

		$tag_names = array_values(array_filter(array_map('trim', explode(',', (string) $in->post('tags')))));

		$featured = (int) $in->post('featured_media_id');
		if ($featured && ! $this->media_model->find($featured))
		{
			$featured = 0;
		}

		// Excerpt: kosong = otomatis; teks yang tidak diubah = pertahankan HTML asli (hasil impor WordPress).
		$excerpt_input = trim((string) $in->post('excerpt'));
		if ($excerpt_input === '')
		{
			$excerpt = auto_excerpt(wp_content($content));
		}
		elseif ($post && isset($post['excerpt']) && $excerpt_input === $this->excerpt_text($post))
		{
			$excerpt = $post['excerpt'];
		}
		else
		{
			$excerpt = '<p>'.htmlspecialchars($excerpt_input, ENT_NOQUOTES, 'UTF-8')."</p>\n";
		}

		$data = array(
			'title'             => $title,
			'slug'              => $slug,
			'content'           => $content,
			'excerpt'           => $excerpt,
			'author_id'         => $author_id,
			'featured_media_id' => $featured ? $featured : NULL,
			'status'            => $status,
			'published_at'      => $published_at ? $published_at->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
			'modified_at'       => date('Y-m-d H:i:s'),
		);

		return array($data, $category_ids, $tag_names, $errors);
	}

	/**
	 * Teks excerpt untuk form; kosong jika sama dengan excerpt otomatis.
	 */
	protected function excerpt_text(array $post)
	{
		if ( ! isset($post['excerpt']) OR $post['excerpt'] === auto_excerpt(wp_content($post['content'])))
		{
			return '';
		}

		return trim(html_entity_decode(strip_tags($post['excerpt']), ENT_QUOTES, 'UTF-8'));
	}
}
