<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Post (berita/pengumuman/artikel) dan arsip kategori, tag, author.
 * Markup mengikuti tema Blocksy di WordPress (lihat views/posts/).
 */
class Posts extends MY_Controller {

	const BODY_TAIL = 'wp-custom-logo wp-embed-responsive';
	const BODY_THEME = 'wp-theme-blocksy elementor-default elementor-kit-9';

	public function __construct()
	{
		parent::__construct();
		$this->load->model('post_model');
		$this->config->load('site');
	}

	/**
	 * /<slug> — satu post.
	 */
	public function single($slug)
	{
		$post = $this->post_model->find_published($slug);
		if ( ! $post)
		{
			show_404();
		}

		$terms = $this->post_model->terms_for(array($post['id']));
		$categories = $terms[$post['id']]['category'];
		$tags = $terms[$post['id']]['post_tag'];
		$content = wp_content($post['content']);
		$elementor = (strpos($content, 'data-elementor-type=') !== FALSE);

		$prev = $this->post_model->adjacent($post, 'prev');
		$next = $this->post_model->adjacent($post, 'next');
		$media = $this->post_model->media(array($prev ? $prev['featured_media_id'] : NULL, $next ? $next['featured_media_id'] : NULL));
		foreach (array('prev', 'next') as $side)
		{
			if (${$side})
			{
				$id = ${$side}['featured_media_id'];
				${$side}['thumbnail'] = ($id && isset($media[$id])) ? wp_post_thumbnail($media[$id], 'medium', '1/1') : '';
			}
		}

		$menu_active = array();
		foreach ($categories as $c)
		{
			$item = $this->menu_item_for($c['slug']);
			if ($item)
			{
				$menu_active[$item] = 'current-post-ancestor current-menu-parent current-post-parent';
			}
		}

		$this->render(array(
			'view'                   => 'posts/single',
			'title'                  => wp_document_title(array($post['title'], $this->config->item('site_title'))),
			'body_attrs'             => ' class="wp-singular post-template-default single single-post postid-'.$post['id'].' single-format-standard '
				.self::BODY_TAIL.' '.self::BODY_THEME.($elementor ? ' elementor-page elementor-page-'.$post['id'] : '').' ct-elementor-default-template"'
				.' data-link="type-2" data-prefix="single_blog_post" data-header="type-1:sticky" data-footer="type-1" itemscope="itemscope" itemtype="https://schema.org/Blog"',
			'head'                   => $post['layout_head'] ? $post['layout_head'] : 'post',
			'foot'                   => $post['layout_foot'] ? $post['layout_foot'] : (preg_match('/class="pdfemb-viewer"|class=\x27w3eden\x27/', $content) ? 'post-pdf' : 'post'),
			'menu_active'            => $menu_active,
			'img_hints'              => $this->single_img_hints($content, $elementor),
			'footer_logo_post_image' => TRUE,
		), array(
			'post'        => $post,
			'content'     => $content,
			'categories'  => $categories,
			'tags'        => $tags,
			'post_class'  => wp_post_class($post, $categories, $tags),
			'share_url'   => wp_encode_uri_component(site_url($post['slug']).'/'),
			'share_title' => wp_encode_uri_component(wp_texturize($post['title'])),
			'prev'        => $prev,
			'next'        => $next,
		));
	}

	/**
	 * /category/<slug>[/page/N]
	 */
	public function category($slug, $page = 1)
	{
		$term = $this->post_model->find_term('category', $slug);
		if ( ! $term)
		{
			show_404();
		}

		$menu_active = array();
		$item = $this->menu_item_for($slug);
		if ($item)
		{
			$menu_active[$item] = 'current-menu-item';
			$menu_active[$this->config->item('menu_category_parent')] = 'current-menu-ancestor current-menu-parent';
		}

		$this->archive('category', $term['name'], 'category/'.$slug, array('term_id' => $term['id']), $page, array(
			'classes'     => 'category category-'.$slug.' category-'.$term['id'],
			'prefix'      => 'categories',
			'label'       => 'Category',
			'feed_title'  => html_escape($term['name']).' Category Feed',
			'menu_active' => $menu_active,
		));
	}

	/**
	 * /tag/<slug>[/page/N]
	 */
	public function tag($slug, $page = 1)
	{
		$term = $this->post_model->find_term('post_tag', $slug);
		if ( ! $term)
		{
			show_404();
		}

		$this->archive('tag', $term['name'], 'tag/'.$slug, array('term_id' => $term['id']), $page, array(
			'classes'    => 'tag tag-'.$slug.' tag-'.$term['id'],
			'prefix'     => 'categories',
			'label'      => 'Tag',
			'feed_title' => html_escape($term['name']).' Tag Feed',
		));
	}

	/**
	 * /author/<slug>[/page/N]
	 */
	public function author($slug, $page = 1)
	{
		$author = $this->post_model->find_author($slug);
		if ( ! $author)
		{
			show_404();
		}

		$this->archive('author', $author['display_name'], 'author/'.$slug, array('author_id' => $author['id']), $page, array(
			'classes'    => 'author author-'.$slug.' author-'.$author['id'],
			'prefix'     => 'author',
			'label'      => '',
			'feed_title' => 'Posts by '.html_escape($author['display_name']).' Feed',
			'author'     => $author,
		));
	}

	protected function archive($type, $name, $base_path, array $filter, $page, array $opt)
	{
		$page = (int) $page;
		if ($page < 1 OR ($page === 1 && $this->uri->segment(3) === 'page'))
		{
			redirect($base_path, 'location', 301);
		}

		list($posts, $total) = $this->post_model->archive($filter, $page);
		$total_pages = max(1, (int) ceil($total / Post_model::PER_PAGE));
		if ($page > $total_pages)
		{
			show_404();
		}

		$ids = array_column($posts, 'id');
		$terms = $this->post_model->terms_for($ids);
		$media = $this->post_model->media(array_column($posts, 'featured_media_id'));

		foreach ($posts as $i => $p)
		{
			$posts[$i]['categories'] = $terms[$p['id']]['category'];
			$posts[$i]['post_class'] = wp_post_class($p, $terms[$p['id']]['category'], $terms[$p['id']]['post_tag']);
			$posts[$i]['thumbnail'] = ($p['featured_media_id'] && isset($media[$p['featured_media_id']]))
				? wp_post_thumbnail($media[$p['featured_media_id']], 'medium_large', '4/3') : '';
		}

		$paged = ($page > 1);
		$title = wp_document_title(array_merge(array($name), $paged ? array('Page '.$page) : array(), array($this->config->item('site_title'))));
		$body = 'archive '.($paged ? 'paged ' : '').$opt['classes'].' '.self::BODY_TAIL
			.($paged ? ' paged-'.$page.' '.$type.'-paged-'.$page : '').' '.self::BODY_THEME.' ct-elementor-default-template';

		$author = isset($opt['author']) ? $opt['author'] : NULL;

		$this->render(array(
			'view'                   => 'posts/archive',
			'title'                  => $title,
			'body_attrs'             => ' class="'.$body.'" data-link="type-2" data-prefix="'.$opt['prefix'].'" data-header="type-1:sticky" data-footer="type-1"',
			'head'                   => 'archive',
			'foot'                   => 'archive',
			'menu_active'            => isset($opt['menu_active']) ? $opt['menu_active'] : array(),
			'img_hints'              => array(
				'header:0' => 'fetchpriority="high" ',
				'header:2' => 'fetchpriority="high" ',
			) + ($author ? array('footer:0' => 'loading="lazy" ') : array()),
			'footer_logo_post_image' => TRUE,
		), array(
			'archive_type'    => $type,
			'archive_label'   => $opt['label'],
			'archive_name'    => $name,
			'feed_title'      => $opt['feed_title'],
			'feed_path'       => $base_path.'/feed',
			'layout'          => $author ? 'grid' : 'simple',
			'card_view'       => $author ? 'posts/_card_grid' : 'posts/_card_simple',
			'author'          => $author,
			'author_articles' => $author ? $this->post_model->count_published_by_author($author['id']) + $author['post_count_offset'] : 0,
			'posts'           => $posts,
			'base_path'       => $base_path,
			'page'            => $page,
			'total_pages'     => $total_pages,
		));
	}

	/**
	 * Atribut fetchpriority/loading gambar di header & sidebar, mengikuti aturan WordPress
	 * (wp_get_loading_optimization_attributes): 3 gambar pertama tidak lazy, gambar besar pertama
	 * tanpa fetchpriority mendapat fetchpriority="high". Gambar konten (yang punya width & height,
	 * bukan Elementor) dihitung lebih dulu karena Blocksy merender konten sebelum header.
	 */
	protected function single_img_hints($content, $elementor)
	{
		$count = 0;
		if ( ! $elementor && preg_match_all('/<img\b[^>]*>/', $content, $m))
		{
			foreach ($m[0] as $img)
			{
				if (strpos($img, 'width=') !== FALSE && strpos($img, 'height=') !== FALSE)
				{
					$count++;
				}
			}
		}
		$priority = (strpos($content, 'fetchpriority="high"') !== FALSE);

		$next = function ($large) use (&$count, &$priority) {
			$count++;
			if ($count > 3)
			{
				return 'loading="lazy" ';
			}
			if ( ! $priority && $large)
			{
				$priority = TRUE;
				return 'fetchpriority="high" ';
			}
			return '';
		};

		// Logo sticky & default dirender sekali lalu dipakai ulang di header mobile.
		$sticky = $next(TRUE);
		$default = $next(TRUE);
		$hints = array('header:0' => $sticky, 'header:1' => $default, 'header:2' => $sticky, 'header:3' => $default);
		for ($i = 0; $i < 5; $i++)
		{
			$hints['sidebar:'.$i] = $next(FALSE);
		}
		$hints['footer:0'] = $next(FALSE);

		return array_filter($hints);
	}

	protected function menu_item_for($category_slug)
	{
		$items = $this->config->item('menu_category_items');

		return isset($items[$category_slug]) ? $items[$category_slug] : NULL;
	}
}
