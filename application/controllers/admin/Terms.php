<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kelola kategori (/admin/terms/category) dan tag (/admin/terms/tag).
 */
class Terms extends Admin_Controller {

	protected $taxonomies = array(
		'category' => array('taxonomy' => 'category', 'label' => 'Kategori', 'path' => 'category'),
		'tag'      => array('taxonomy' => 'post_tag', 'label' => 'Tag', 'path' => 'tag'),
	);

	public function __construct()
	{
		parent::__construct();
		$this->load->model('term_model');
	}

	public function index($type = 'category')
	{
		$tax = $this->tax($type);
		$this->view('admin/terms/index', array(
			'title' => $tax['label'],
			'type'  => $type,
			'tax'   => $tax,
			'terms' => $this->term_model->all($tax['taxonomy']),
		));
	}

	public function create($type = 'category')
	{
		$this->form($type, NULL);
	}

	public function edit($type = 'category', $id = NULL)
	{
		$term = $this->term_model->find($id);
		if ( ! $term OR $term['taxonomy'] !== $this->tax($type)['taxonomy'])
		{
			show_404();
		}
		$this->form($type, $term);
	}

	public function delete($type = 'category', $id = NULL)
	{
		$this->require_post();
		$tax = $this->tax($type);
		$term = $this->term_model->find($id);
		if ( ! $term OR $term['taxonomy'] !== $tax['taxonomy'])
		{
			show_404();
		}
		$this->term_model->delete($term['id']);
		$this->flash('success', $tax['label'].' "'.$term['name'].'" dihapus.');
		redirect('admin/terms/index/'.$type);
	}

	protected function form($type, $term)
	{
		$tax = $this->tax($type);
		$errors = array();

		if ($this->input->method() === 'post')
		{
			$name = trim((string) $this->input->post('name'));
			$slug = slugify(trim((string) $this->input->post('slug')) !== '' ? $this->input->post('slug') : $name);
			$data = array('taxonomy' => $tax['taxonomy'], 'name' => $name, 'slug' => $slug, 'description' => trim((string) $this->input->post('description')));

			if ($name === '')
			{
				$errors[] = 'Nama wajib diisi.';
			}
			if ($slug === '')
			{
				$errors[] = 'Slug tidak valid.';
			}
			elseif ($this->term_model->slug_taken($tax['taxonomy'], $slug, $term ? $term['id'] : NULL))
			{
				$errors[] = 'Slug "'.$slug.'" sudah dipakai.';
			}

			if (empty($errors))
			{
				if ($term)
				{
					$this->term_model->update($term['id'], $data);
				}
				else
				{
					$this->term_model->create($data);
				}
				$this->flash('success', $tax['label'].' disimpan.');
				redirect('admin/terms/index/'.$type);
			}
			$term = array_merge($term ? $term : array(), $data);
		}

		$this->view('admin/terms/form', array(
			'title'  => ($term && ! empty($term['id']) ? 'Edit ' : 'Tambah ').strtolower($tax['label']),
			'type'   => $type,
			'tax'    => $tax,
			'term'   => $term,
			'errors' => $errors,
		));
	}

	protected function tax($type)
	{
		if ( ! isset($this->taxonomies[$type]))
		{
			show_404();
		}

		return $this->taxonomies[$type];
	}
}
