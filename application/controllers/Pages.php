<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Halaman statis hasil migrasi (Page WordPress).
 * Daftar halaman ada di application/config/pages.php.
 */
class Pages extends MY_Controller {

	public function index()
	{
		// Shortlink WordPress lama: /?p=<ID> -> permalink post.
		$id = $this->input->get('p');
		if ($id !== NULL && ctype_digit((string) $id))
		{
			$this->load->model('post_model');
			$post = $this->post_model->find_published_id($id);
			if ( ! $post)
			{
				show_404();
			}
			redirect($post['slug'], 'location', 301);
		}

		$this->show('home');
	}

	public function show($slug)
	{
		$this->config->load('pages');
		$pages = $this->config->item('pages');

		if ($slug === 'home' && $this->uri->uri_string() !== '')
		{
			redirect('', 'location', 301);
		}

		if ( ! isset($pages[$slug]))
		{
			show_404();
		}

		$this->render($pages[$slug]);
	}
}
