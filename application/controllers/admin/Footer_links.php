<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Footer_links extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('footer_link_model');
	}

	public function index()
	{
		$this->view('admin/footer_links/index', array(
			'title' => 'Link Footer',
			'links' => $this->footer_link_model->all(),
		));
	}

	public function create()
	{
		$this->form(NULL);
	}

	public function edit($id = NULL)
	{
		$link = $this->footer_link_model->find($id);
		if ( ! $link)
		{
			show_404();
		}
		$this->form($link);
	}

	public function delete($id = NULL)
	{
		$this->require_post();
		$link = $this->footer_link_model->find($id);
		if ( ! $link)
		{
			show_404();
		}
		$this->footer_link_model->delete($link['id']);
		$this->flash('success', 'Link "'.$link['title'].'" dihapus.');
		redirect('admin/footer_links/index');
	}

	protected function form($link)
	{
		$errors = array();

		if ($this->input->method() === 'post')
		{
			$title = trim((string) $this->input->post('title'));
			$url = trim((string) $this->input->post('url'));
			$order_num = (int) $this->input->post('order_num');

			$data = array('title' => $title, 'url' => $url, 'order_num' => $order_num);

			if ($title === '')
			{
				$errors[] = 'Judul wajib diisi.';
			}
			if ($url === '')
			{
				$errors[] = 'URL wajib diisi.';
			}
			elseif ( ! is_safe_url($url))
			{
				$errors[] = 'URL harus diawali https://, http://, /, #, mailto:, atau tel:.';
			}

			if (empty($errors))
			{
				if ($link)
				{
					$this->footer_link_model->update($link['id'], $data);
					$this->flash('success', 'Link diperbarui.');
				}
				else
				{
					$this->footer_link_model->create($data);
					$this->flash('success', 'Link ditambahkan.');
				}
				redirect('admin/footer_links/index');
			}
			$link = array_merge($link ? $link : array(), $data);
		}

		$this->view('admin/footer_links/form', array(
			'title'  => ($link && ! empty($link['id']) ? 'Edit ' : 'Tambah ') . 'Link Footer',
			'link'   => $link,
			'errors' => $errors,
		));
	}
}
