<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kelola menu utama situs (header desktop + menu mobile). Khusus peran admin.
 */
class Menu extends Admin_Controller {

	protected $roles = array('admin');

	protected $type_labels = array(
		'page'     => 'Halaman',
		'category' => 'Kategori',
		'custom'   => 'URL / label',
	);

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('menu_model', 'term_model'));
	}

	public function index()
	{
		$this->view('admin/menu/index', array(
			'title'       => 'Menu',
			'items'       => $this->menu_model->flat(),
			'pages'       => $this->page_options(),
			'type_labels' => $this->type_labels,
		));
	}

	public function create()
	{
		$this->form(NULL);
	}

	public function edit($id = NULL)
	{
		$item = $this->menu_model->find($id);
		if ( ! $item)
		{
			show_404();
		}
		$this->form($item);
	}

	public function move($id = NULL, $direction = 'up')
	{
		$this->require_post();
		if ( ! $this->menu_model->find($id))
		{
			show_404();
		}
		$this->menu_model->move($id, ($direction === 'down') ? 1 : -1);
		redirect('admin/menu#item-'.(int) $id);
	}

	public function delete($id = NULL)
	{
		$this->require_post();
		$item = $this->menu_model->find($id);
		if ( ! $item)
		{
			show_404();
		}
		$sub = count($this->menu_model->descendant_ids($item['id'])) - 1;
		$this->menu_model->delete($item['id']);
		$this->flash('success', 'Item "'.$item['title'].'" dihapus'.($sub ? ' beserta '.$sub.' sub-item.' : '.'));
		redirect('admin/menu');
	}

	protected function form($item)
	{
		$errors = array();
		$pages = $this->page_options();
		$categories = $this->term_model->all('category');

		if ($this->input->method() === 'post')
		{
			list($data, $errors) = $this->validate($item, $pages, $categories);
			if (empty($errors))
			{
				if ($item)
				{
					$this->menu_model->update($item['id'], $data);
					$id = $item['id'];
				}
				else
				{
					$id = $this->menu_model->create($data);
				}
				$this->flash('success', 'Item menu disimpan.');
				redirect('admin/menu#item-'.(int) $id);
			}
			$item = array_merge($item ? $item : array(), $data);
		}
		elseif ( ! $item)
		{
			// Tombol "tambah sub-item" mengisi induk lewat ?parent=ID.
			$parent = (int) $this->input->get('parent');
			$item = array('parent_id' => $this->menu_model->find($parent) ? $parent : NULL, 'type' => 'page');
		}

		$exclude = ! empty($item['id']) ? $this->menu_model->descendant_ids($item['id']) : array();
		$parents = array_filter($this->menu_model->flat(), function ($row) use ($exclude) {
			return ! in_array((int) $row['id'], $exclude, TRUE);
		});

		$this->view('admin/menu/form', array(
			'title'       => ! empty($item['id']) ? 'Edit item menu' : 'Tambah item menu',
			'item'        => $item,
			'errors'      => $errors,
			'pages'       => $pages,
			'categories'  => $categories,
			'parents'     => $parents,
			'type_labels' => $this->type_labels,
			'url_input'   => (isset($item['url']) && $item['url'] !== NULL) ? wp_content($item['url']) : '',
		));
	}

	/**
	 * Mengembalikan [data kolom menu_items, error].
	 */
	protected function validate($item, array $pages, array $categories)
	{
		$in = $this->input;
		$errors = array();
		$type = (string) $in->post('type');

		$data = array(
			'title'     => trim((string) $in->post('title')),
			'type'      => isset($this->type_labels[$type]) ? $type : 'custom',
			'parent_id' => (int) $in->post('parent_id') ?: NULL,
			'object_id' => NULL,
			'slug'      => NULL,
			'url'       => NULL,
		);

		if ($data['title'] === '')
		{
			$errors[] = 'Label wajib diisi.';
		}
		elseif (mb_strlen($data['title']) > 255)
		{
			$errors[] = 'Label maksimal 255 karakter.';
		}

		if ($data['parent_id'])
		{
			$exclude = ($item && ! empty($item['id'])) ? $this->menu_model->descendant_ids($item['id']) : array();
			if ( ! $this->menu_model->find($data['parent_id']) OR in_array($data['parent_id'], $exclude, TRUE))
			{
				$errors[] = 'Induk tidak valid (tidak bisa memilih item ini sendiri atau sub-itemnya).';
			}
		}

		if ($data['type'] === 'page')
		{
			$slug = (string) $in->post('page');
			if ( ! isset($pages[$slug]))
			{
				$errors[] = 'Pilih halaman.';
			}
			else
			{
				$data['slug'] = $slug;
				$data['object_id'] = $pages[$slug]['object_id'];
			}
		}
		elseif ($data['type'] === 'category')
		{
			$term_id = (int) $in->post('category');
			if ( ! in_array($term_id, array_map('intval', array_column($categories, 'id')), TRUE))
			{
				$errors[] = 'Pilih kategori.';
			}
			$data['object_id'] = $term_id;
		}
		else
		{
			$url = trim((string) $in->post('url'));
			if ($url !== '')
			{
				// URL situs ini (atau domain utama stmi.ac.id) disimpan sebagai token agar tetap benar di server lain.
				$url = preg_replace('#^(?:https?:)?//(?:www\.)?stmi\.ac\.id(?![\w.-])/?#i', '{base_url}', $url);
				if (strpos($url, base_url()) === 0)
				{
					$url = '{base_url}'.substr($url, strlen(base_url()));
				}
				elseif ($url[0] === '/' && substr($url, 0, 2) !== '//')
				{
					$url = '{base_url}'.ltrim($url, '/');
				}

				if ( ! preg_match('#^(\#.*|\{base_url\}.*|https?://\S+|mailto:\S+|tel:\S+)$#i', $url))
				{
					$errors[] = 'URL harus diawali https://, http://, /, #, mailto:, atau tel:.';
				}
				elseif (mb_strlen($url) > 500)
				{
					$errors[] = 'URL maksimal 500 karakter.';
				}
				$data['url'] = $url;
			}
		}

		return array($data, $errors);
	}

	/**
	 * Halaman yang bisa dipilih: semua halaman di tabel pages (termasuk beranda = slug '') dan halaman
	 * yang sudah ada di menu tapi belum dimigrasi. slug => [title, object_id, migrated, status].
	 */
	protected function page_options()
	{
		$this->load->model('page_model');
		$options = array();

		foreach ($this->page_model->options() as $slug => $page)
		{
			$options[$slug] = $page + array('migrated' => TRUE);
		}

		foreach ($this->menu_model->all() as $row)
		{
			if ($row['type'] === 'page' && ! isset($options[$row['slug']]))
			{
				$options[$row['slug']] = array('title' => $row['title'], 'object_id' => $row['object_id'], 'migrated' => FALSE, 'status' => 'publish');
			}
		}

		uasort($options, function ($a, $b) {
			return strcasecmp($a['title'], $b['title']);
		});

		return $options;
	}
}
