<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller dasar untuk halaman publik: merender layout hasil migrasi WordPress.
 */
class MY_Controller extends CI_Controller {

	/**
	 * Render halaman ke dalam layout.
	 *
	 * $page (lihat application/config/pages.php):
	 *   view        : view isi halaman (bagian di antara header dan footer)
	 *   title       : isi <title> (sudah dalam bentuk HTML)
	 *   body_attrs  : atribut tag <body> apa adanya
	 *   head / foot : nama varian aset di views/layouts/head/ dan views/layouts/foot/
	 *   menu_context: halaman yang sedang dibuka, untuk menandai menu aktif (lihat Menu_model::active_for())
	 *                 array('page' => slug) | array('category' => id) | array('post_categories' => [id])
	 *   img_hints   : fetchpriority/loading per <img> di header/drawer/footer (lihat wp_img_hint())
	 */
	protected function render(array $page, array $data = array())
	{
		$uri = $this->uri->uri_string();

		$this->load->model('footer_link_model');
		$this->load->model('contact_model');
		$this->load->model('menu_model');

		$data = array_merge(array(
			'canonical'              => current_url(),
			'gt_orig_url'            => ($uri === '') ? '/' : '/'.$uri.'/',
			'menu_context'           => array(),
			'main_menu'              => $this->menu_model->tree(),
			'img_hints'              => array(),
			'footer_logo_post_image' => FALSE,
			'footer_links'           => $this->footer_link_model->all(),
			'contacts'               => $this->contact_model->get_all(),
		), $page, $data);

		$html = $this->load->view('layouts/main', $data, TRUE);
		$active = $this->menu_model->active_for($data['menu_context'] + array('url' => current_url()));

		$this->output->set_output(wp_menu_active($html, $active));
	}

	/**
	 * Halaman hasil pencarian seperti WordPress + Blocksy (/?s=, /page/N/?s=, /search/<kata>/).
	 * Mencari post, paket download, dan halaman statis (Search_model), 5 hasil per halaman.
	 */
	public function render_search($query, $page = 1)
	{
		$this->load->model(array('search_model', 'post_model', 'media_model'));
		$this->config->load('site');

		$query = trim((string) $query);
		$page = max(1, (int) $page);
		list($rows, $total) = $this->search_model->search($query, $page);
		$total_pages = (int) ceil($total / Search_model::PER_PAGE);
		if ($page > 1 && $page > $total_pages)
		{
			return $this->render_not_found();
		}

		$post_ids = array();
		$media_ids = array();
		$author_ids = array();
		foreach ($rows as $r)
		{
			if ($r['kind'] === 'post')
			{
				$post_ids[] = $r['id'];
			}
			$media_ids[] = $r['featured_media_id'];
			$author_ids[] = $r['author_id'];
		}
		$terms = $this->post_model->terms_for($post_ids);
		$media = $this->post_model->media($media_ids);
		$authors = array();
		foreach ($this->db->where_in('id', $author_ids ? array_unique($author_ids) : array(0))->get('authors')->result_array() as $a)
		{
			$authors[$a['id']] = $a;
		}

		$results = array();
		foreach ($rows as $r)
		{
			$has_thumb = $r['featured_media_id'] && isset($media[$r['featured_media_id']]);
			$item = array(
				'id'           => $r['id'],
				'title'        => $r['title'],
				'slug'         => $r['slug'],
				'published_at' => $r['published_at'],
				'author_slug'  => $authors[$r['author_id']]['slug'],
				'author_name'  => $authors[$r['author_id']]['display_name'],
				'thumbnail'    => $has_thumb ? wp_post_thumbnail($media[$r['featured_media_id']], 'medium_large', '4/3') : '',
			);

			if ($r['kind'] === 'post')
			{
				$post = $this->post_model->find($r['id']);
				$item['categories'] = $terms[$r['id']]['category'];
				$item['post_class'] = wp_post_class($post, $terms[$r['id']]['category'], $terms[$r['id']]['post_tag']);
				$item['excerpt'] = $post['excerpt'];
				$item['card_view'] = 'posts/_card_grid';
			}
			elseif ($r['kind'] === 'download')
			{
				$item['url'] = site_url('download/'.$r['slug']);
				$item['post_class'] = 'post-'.$r['id'].' wpdmpro type-wpdmpro status-publish'.($has_thumb ? ' has-post-thumbnail' : '').' hentry';
				$item['card_view'] = 'search/_card_download';
			}
			else
			{
				$item['url'] = site_url($r['slug'] === 'home' ? '' : $r['slug']);
				$item['post_class'] = 'post-'.$r['id'].' page type-page status-publish'.($has_thumb ? ' has-post-thumbnail' : '').' hentry';
				$item['excerpt'] = wp_excerpt_from_html(wp_content($r['body']));
				$item['card_view'] = 'search/_card_page';
			}
			$results[] = $item;
		}

		$paged = ($page > 1);
		$title = 'Search Results for &#8220;'.html_escape($query).'&#8221;';

		$this->render(array(
			'view'       => 'search/index',
			'title'      => wp_document_title(array_merge(array($title), $paged ? array('Page '.$page) : array(), array($this->config->item('site_title')))),
			'body_attrs' => ' class="search '.($total ? 'search-results' : 'search-no-results').($paged ? ' paged' : '')
				.' wp-custom-logo wp-embed-responsive'.($paged ? ' paged-'.$page.' search-paged-'.$page : '')
				.' wp-theme-blocksy elementor-default elementor-kit-9 ct-elementor-default-template"'
				.' data-link="type-2" data-prefix="search" data-header="type-1:sticky" data-footer="type-1"',
			'head'       => 'search',
			'foot'       => 'search',
			'img_hints'  => array('header:0' => 'fetchpriority="high" ', 'header:2' => 'fetchpriority="high" ', 'footer:0' => 'loading="lazy" '),
			'footer_logo_post_image' => (bool) $total,
		), array(
			'query'        => $query,
			'query_string' => '?s='.urlencode($query),
			'results'      => $results,
			'total'        => $total,
			'page'         => $page,
			'total_pages'  => $total_pages,
			// WordPress/GTranslate memakai REQUEST_URI apa adanya pada halaman pencarian.
			'gt_orig_url'  => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/',
		));
	}

	/**
	 * Halaman statis dari tabel pages (view NULL) dengan template halaman standar Blocksy:
	 * hero, judul, author, breadcrumb, isi, tombol share, sidebar (views/pages/_page.php).
	 */
	public function render_page(array $page)
	{
		$this->load->model(array('page_model', 'author_model', 'download_model'));
		$this->config->load('site');

		$content = $this->download_model->render_shortcodes(wp_content($page['content']));
		list($head, $foot) = $this->page_model->layout_for($page['content'], $page['layout_head'], $page['layout_foot']);
		list($img_hints, $avatar_hint) = $this->page_model->img_hints($content);
		// peraturan & perkin: konten biasa, tetapi WordPress tetap memuat Elementor (varian head-nya) dan kelas elementor-page.
		$elementor = (Page_model::is_elementor($page['content']) || $this->page_model->variant_has('head', $head, 'elementor-frontend-css'));
		$id = (int) $page['id'];
		$full = ($page['template'] === 'full-width');

		if ($full)
		{
			// Template "Elementor Full Width": tanpa hero/judul/sidebar; logo header tetap fetchpriority, tanpa avatar.
			$body = 'wp-singular page-template page-template-elementor_header_footer page page-id-'.$id.' wp-custom-logo wp-embed-responsive'
				.' wp-theme-blocksy elementor-default elementor-template-full-width elementor-kit-9'.($elementor ? ' elementor-page elementor-page-'.$id : '');
			$img_hints = array('header:0' => 'fetchpriority="high" ', 'header:2' => 'fetchpriority="high" ');
		}
		else
		{
			$body = 'wp-singular page-template-default page page-id-'.$id.' wp-custom-logo wp-embed-responsive wp-theme-blocksy'
				.' elementor-default elementor-kit-9 '.($elementor ? 'elementor-page elementor-page-'.$id.' ' : '').'ct-elementor-default-template';
		}

		$this->render(array(
			'view'       => $full ? 'pages/_page_full' : 'pages/_page',
			'title'      => wp_document_title(array($page['title'], $this->config->item('site_title'))),
			'body_attrs' => ' class="'.$body.'"'
				.' data-link="type-2" data-prefix="single_page" data-header="type-1:sticky" data-footer="type-1" itemscope="itemscope" itemtype="https://schema.org/WebPage"',
			'head'       => $head,
			'foot'       => $foot,
			'img_hints'  => $img_hints,
		), array(
			'page'          => $page,
			'content'       => $content,
			'author'        => $this->author_model->find($page['author_id']),
			'avatar_hint'   => $avatar_hint,
			'has_thumbnail' => ! empty($page['featured_media_id']),
			'share_url'     => wp_encode_uri_component(site_url($page['slug']).'/'),
			'share_title'   => wp_encode_uri_component(wp_texturize($page['title'])),
			'menu_context'  => array('page' => $page['slug']),
		));
	}

	/**
	 * Halaman 404 bergaya WordPress ("Oops! That page can't be found.", dari clone js15_as.html) dengan status 404.
	 * Dipanggil lewat 404_override (Pages::not_found) dan MY_Exceptions::show_404().
	 */
	public function render_not_found()
	{
		$this->config->load('pages');
		$pages = $this->config->item('pages');

		$this->output->set_status_header(404);
		$this->render($pages['error-404'], array(
			// WordPress/GTranslate memakai REQUEST_URI apa adanya pada halaman 404.
			'gt_orig_url' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/',
		));
	}
}

/**
 * Controller dasar panel admin (/admin): wajib login, memuat session & form helper.
 */
class Admin_Controller extends CI_Controller {

	/** @var array|null author yang sedang login */
	protected $user;

	/** Peran yang boleh mengakses controller ini. */
	protected $roles = array('admin', 'editor');

	public function __construct()
	{
		parent::__construct();
		$this->load->library(array('session', 'form_validation'));
		$this->load->helper(array('form', 'admin'));
		$this->load->model('author_model');

		$id = $this->session->userdata('admin_id');
		$this->user = $id ? $this->author_model->find($id) : NULL;

		if ( ! $this->user OR ! $this->user['is_active'] OR $this->user['password_hash'] === NULL)
		{
			$this->session->unset_userdata('admin_id');
			$this->session->set_userdata('admin_redirect', $this->uri->uri_string());
			redirect('admin/login');
		}

		if ( ! in_array($this->user['role'], $this->roles, TRUE))
		{
			show_error('Anda tidak punya akses ke halaman ini.', 403, 'Akses ditolak');
		}

		$this->output->set_header('Cache-Control: no-store');
	}

	/**
	 * Render halaman admin di dalam views/admin/layout.php.
	 */
	protected function view($view, array $data = array())
	{
		$data['user'] = $this->user;
		$data['content_view'] = $view;
		$this->load->view('admin/layout', $data);
	}

	protected function flash($type, $message)
	{
		$this->session->set_flashdata('flash', array('type' => $type, 'message' => $message));
	}

	/* ------------------------------------------------------------------
	 * Editor blok Elementor (form halaman & post; lihat libraries/Elementor_doc.php)
	 * ------------------------------------------------------------------ */

	/** Pesan & anchor setelah operasi blok (duplikat/hapus/naik/turun). */
	protected $op_message = NULL;
	protected $op_anchor = NULL;

	/**
	 * Terapkan isi editor blok ke konten Elementor tersimpan. Mengembalikan [konten baru, error].
	 */
	protected function apply_blocks($stored)
	{
		$this->load->library('elementor_doc');
		$this->load->model('media_model');
		if ((string) $this->input->post('content_hash') !== Elementor_doc::hash($stored))
		{
			return array($stored, array('Isi halaman sudah berubah sejak editor dibuka (mungkin disimpan dari tab lain). Muat ulang halaman ini, lalu ulangi perubahannya.'));
		}

		$doc = Elementor_doc::from($stored);
		$values = array();
		foreach ((array) $this->input->post('blocks') as $key => $value)
		{
			if (is_string($value))
			{
				$values[$key] = content_to_tokens($value);
			}
		}
		$doc->apply_fields($values, function (array $field, $media_id) {
			return $this->image_html($field, $media_id);
		});

		$op = explode('|', (string) $this->input->post('block_op'), 2);
		if (count($op) === 2)
		{
			$error = $doc->operate($op[0], $op[1]);
			if ($error !== NULL)
			{
				return array($stored, array($error));
			}
			$labels = array('dup' => 'Blok diduplikat', 'del' => 'Blok dihapus', 'up' => 'Blok dipindah ke atas', 'down' => 'Blok dipindah ke bawah');
			$this->op_message = (isset($labels[$op[0]]) ? $labels[$op[0]] : 'Blok diubah').'; halaman disimpan.';
			$this->op_anchor = 'blk-'.str_replace(':', '-', $op[1]);
		}

		return array($doc->html(), array());
	}

	/**
	 * Isi baru widget gambar Elementor: tag <img> diganti gambar dari pustaka media (ukuran sama dengan kelas size-*
	 * aslinya bila ada, srcset dihitung ulang bila aslinya punya srcset). Link lightbox ke file gambar lama ikut diganti.
	 */
	protected function image_html(array $field, $media_id)
	{
		$media = $this->media_model->find($media_id);
		if ( ! $media OR strpos($media['mime_type'], 'image/') !== 0 OR ! preg_match('/<img\b[^>]*>/', $field['value'], $m))
		{
			return NULL;
		}
		$img = $m[0];
		$sizes = (array) json_decode($media['sizes'], TRUE);
		$dir = dirname($media['file']);
		$dir = ($dir === '.') ? '' : $dir.'/';
		$size = preg_match('/\bsize-([a-z0-9_-]+)\b/', $img, $s) ? $s[1] : 'full';
		$src = ($size !== 'full' && isset($sizes[$size]))
			? array('file' => $dir.$sizes[$size]['file'], 'width' => (int) $sizes[$size]['width'], 'height' => (int) $sizes[$size]['height'])
			: array('file' => $media['file'], 'width' => (int) $media['width'], 'height' => (int) $media['height']);
		$url = '{base_url}wp-content/uploads/'.$src['file'];

		$set = function ($tag, $name, $value) {
			$attr = $name.'="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"';
			$replace = function () use ($attr) { return ' '.$attr; };
			return preg_match('/\s'.$name.'="[^"]*"/', $tag)
				? preg_replace_callback('/\s'.$name.'="[^"]*"/', $replace, $tag, 1)
				: preg_replace_callback('/\s*\/?>$/', function () use ($attr) { return ' '.$attr.' />'; }, $tag, 1);
		};
		$new = $set($img, 'src', $url);
		$new = $set($new, 'width', (string) $src['width']);
		$new = $set($new, 'height', (string) $src['height']);
		$new = $set($new, 'alt', (string) $media['alt']);
		$new = preg_replace('/\bwp-image-\d+\b/', 'wp-image-'.(int) $media['id'], $new, 1);
		if (strpos($new, 'srcset=') !== FALSE)
		{
			$srcset = wp_image_srcset($media, $src);
			if ($srcset)
			{
				$new = $set($new, 'srcset', content_to_tokens(implode(', ', $srcset)));
				$new = $set($new, 'sizes', '(max-width: '.$src['width'].'px) 100vw, '.$src['width'].'px');
			}
			else
			{
				$new = preg_replace('/\s(?:srcset|sizes)="[^"]*"/', '', $new);
			}
		}

		$html = str_replace($img, $new, $field['value']);
		// Link lightbox yang menunjuk file gambar lama -> file gambar baru (ukuran penuh).
		$old_file = preg_replace('/-\d+x\d+(?=\.[a-z]+$)/i', '', (string) $field['src']);
		if ($old_file !== '')
		{
			$html = str_replace('href="'.$old_file.'"', 'href="{base_url}wp-content/uploads/'.$media['file'].'"', $html);
		}

		return $html;
	}

	/**
	 * Data view editor blok untuk konten Elementor tersimpan (NULL jika bukan Elementor / belum tersimpan).
	 */
	protected function block_editor_data($content, $saved)
	{
		if ( ! $saved OR strpos((string) $content, 'data-elementor-type=') === FALSE)
		{
			return array('blocks' => NULL, 'content_hash' => '');
		}
		$this->load->library('elementor_doc');

		return array('blocks' => Elementor_doc::from($content)->outline(), 'content_hash' => Elementor_doc::hash($content));
	}

	/**
	 * Tolak request non-POST untuk aksi yang mengubah data (hapus, dsb.).
	 */
	protected function require_post()
	{
		if ($this->input->method() !== 'post')
		{
			show_error('Metode tidak diizinkan.', 405);
		}
	}
}
