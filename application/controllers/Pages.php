<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Halaman statis hasil migrasi (Page WordPress).
 * Daftar halaman ada di application/config/pages.php.
 */
class Pages extends MY_Controller {

	public function index()
	{
		// Pencarian WordPress: /?s=<kata>
		if ($this->input->get('s') !== NULL)
		{
			return $this->render_search((string) $this->input->get('s'));
		}

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

	/**
	 * 404_override: URL yang tidak cocok dengan route mana pun.
	 */
	public function not_found()
	{
		$this->render_not_found();
	}

	public function show($slug)
	{
		$this->config->load('pages');
		$pages = $this->config->item('pages');

		if ($slug === 'home' && $this->uri->uri_string() !== '')
		{
			redirect('', 'location', 301);
		}

		if ( ! isset($pages[$slug]) OR $slug === 'error-404')
		{
			show_404();
		}

		$data = [];
		if ($slug === 'home')
		{
			$this->load->model('home_featured_link_model');
			$this->load->model('home_study_program_model');
			$this->load->model('home_banner_model');
			$this->load->model('home_partner_model');
			$this->load->model('home_option_model');
			
			$data['featured_links'] = $this->home_featured_link_model->all();
			$data['featured_links_grouped'] = $this->home_featured_link_model->grouped_by_row();
			$data['study_programs'] = $this->home_study_program_model->all();
			$data['home_banners'] = $this->home_banner_model->all();
			$data['home_partners'] = $this->home_partner_model->all();
			$data['home_options'] = $this->home_option_model->get_all();
		}

		$data['menu_context'] = array('page' => ($slug === 'home') ? '' : $slug);

		$this->render($pages[$slug], $data);
	}
}
