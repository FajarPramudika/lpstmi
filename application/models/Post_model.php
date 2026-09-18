<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Post (berita, pengumuman, artikel) beserta kategori, tag, author, dan media.
 */
class Post_model extends CI_Model {

	/** Jumlah post per halaman arsip (sama dengan WordPress). */
	const PER_PAGE = 5;

	public function find_published($slug)
	{
		return $this->db->where('slug', $slug)->where('status', 'publish')->get('posts')->row_array();
	}

	public function find_published_id($id)
	{
		return $this->db->where('id', (int) $id)->where('status', 'publish')->get('posts')->row_array();
	}

	/**
	 * Post terbit sebelumnya ('prev', lebih lama) atau sesudahnya ('next', lebih baru).
	 */
	public function adjacent(array $post, $direction)
	{
		$older = ($direction === 'prev');

		return $this->db
			->select('id, slug, title, featured_media_id')
			->where('status', 'publish')
			->where('published_at '.($older ? '<' : '>'), $post['published_at'])
			->order_by('published_at', $older ? 'DESC' : 'ASC')
			->limit(1)
			->get('posts')
			->row_array();
	}

	/**
	 * Kategori & tag untuk sekumpulan post: [post_id => ['category' => [...], 'post_tag' => [...]]].
	 */
	public function terms_for(array $post_ids)
	{
		$out = array();
		foreach ($post_ids as $id)
		{
			$out[$id] = array('category' => array(), 'post_tag' => array());
		}
		if (empty($post_ids))
		{
			return $out;
		}

		$rows = $this->db
			->select('pt.post_id, t.id, t.taxonomy, t.name, t.slug')
			->from('post_terms pt')
			->join('terms t', 't.id = pt.term_id')
			->where_in('pt.post_id', $post_ids)
			->order_by('pt.post_id')
			->order_by('pt.term_order')
			->get()
			->result_array();

		foreach ($rows as $r)
		{
			$out[$r['post_id']][$r['taxonomy']][] = array('id' => $r['id'], 'name' => $r['name'], 'slug' => $r['slug']);
		}

		return $out;
	}

	/**
	 * Media berdasarkan ID: [id => row].
	 */
	public function media(array $ids)
	{
		$ids = array_values(array_filter(array_unique($ids)));
		if (empty($ids))
		{
			return array();
		}

		$out = array();
		foreach ($this->db->where_in('id', $ids)->get('media')->result_array() as $m)
		{
			$out[$m['id']] = $m;
		}

		return $out;
	}

	public function find_term($taxonomy, $slug)
	{
		return $this->db->where('taxonomy', $taxonomy)->where('slug', $slug)->get('terms')->row_array();
	}

	public function find_author($slug)
	{
		return $this->db->where('slug', $slug)->get('authors')->row_array();
	}

	/**
	 * Post terbit untuk arsip: filter ['term_id' => x] atau ['author_id' => x].
	 * Mengembalikan [daftar post (dengan nama/slug author), total].
	 */
	public function archive(array $filter, $page)
	{
		$build = function () use ($filter) {
			$this->db->from('posts p')->where('p.status', 'publish');
			if (isset($filter['term_id']))
			{
				$this->db->join('post_terms pt', 'pt.post_id = p.id')->where('pt.term_id', $filter['term_id']);
			}
			if (isset($filter['author_id']))
			{
				$this->db->where('p.author_id', $filter['author_id']);
			}
		};

		$build();
		$total = $this->db->count_all_results();

		$build();
		$posts = $this->db
			->select('p.*, a.slug AS author_slug, a.display_name AS author_name')
			->join('authors a', 'a.id = p.author_id')
			->order_by('p.published_at', 'DESC')
			->limit(self::PER_PAGE, ($page - 1) * self::PER_PAGE)
			->get()
			->result_array();

		return array($posts, $total);
	}

	/* ------------------------------------------------------------------
	 * Panel admin
	 * ------------------------------------------------------------------ */

	const ADMIN_PER_PAGE = 20;

	/**
	 * Daftar post untuk admin: [baris, total]. Filter: status, category (term id), q (judul).
	 */
	public function admin_list(array $filter, $page)
	{
		$build = function () use ($filter) {
			$this->db->from('posts p')->join('authors a', 'a.id = p.author_id');
			if ( ! empty($filter['status']))
			{
				$this->db->where('p.status', $filter['status']);
			}
			if ( ! empty($filter['category']))
			{
				$this->db->where('EXISTS (SELECT 1 FROM post_terms pt WHERE pt.post_id = p.id AND pt.term_id = '.(int) $filter['category'].')', NULL, FALSE);
			}
			if ( ! empty($filter['q']))
			{
				$this->db->like('p.title', $filter['q']);
			}
		};

		$build();
		$total = $this->db->count_all_results();
		$build();
		$rows = $this->db
			->select('p.id, p.slug, p.title, p.status, p.published_at, p.modified_at, a.display_name AS author_name')
			->order_by('p.published_at', 'DESC')
			->limit(self::ADMIN_PER_PAGE, ($page - 1) * self::ADMIN_PER_PAGE)
			->get()
			->result_array();

		return array($rows, $total);
	}

	public function find($id)
	{
		return $this->db->where('id', (int) $id)->get('posts')->row_array();
	}

	public function slug_taken($slug, $except_id = NULL)
	{
		$this->db->where('slug', $slug);
		if ($except_id)
		{
			$this->db->where('id !=', (int) $except_id);
		}

		return $this->db->count_all_results('posts') > 0;
	}

	/**
	 * Simpan post beserta kategori & tag (urut nama, seperti WordPress). Mengembalikan ID.
	 */
	public function save($id, array $data, array $category_ids, array $tag_ids)
	{
		$this->db->trans_start();

		if ($id)
		{
			$this->db->where('id', (int) $id)->update('posts', $data);
		}
		else
		{
			$this->db->insert('posts', $data);
			$id = $this->db->insert_id();
		}

		$this->db->where('post_id', (int) $id)->delete('post_terms');
		$order = 0;
		foreach (array($category_ids, $tag_ids) as $ids)
		{
			if (empty($ids))
			{
				continue;
			}
			$terms = $this->db->select('id')->where_in('id', array_map('intval', $ids))->order_by('name')->get('terms')->result_array();
			foreach ($terms as $t)
			{
				$this->db->insert('post_terms', array('post_id' => (int) $id, 'term_id' => (int) $t['id'], 'term_order' => $order++));
			}
		}

		$this->db->trans_complete();

		return $this->db->trans_status() ? (int) $id : FALSE;
	}

	public function delete($id)
	{
		return $this->db->where('id', (int) $id)->delete('posts');
	}

	public function term_ids($post_id)
	{
		$out = array('category' => array(), 'post_tag' => array());
		$rows = $this->db->select('t.id, t.taxonomy, t.name')->from('post_terms pt')->join('terms t', 't.id = pt.term_id')
			->where('pt.post_id', (int) $post_id)->order_by('pt.term_order')->get()->result_array();
		foreach ($rows as $r)
		{
			$out[$r['taxonomy']][] = $r;
		}

		return $out;
	}

	public function counts()
	{
		$out = array('publish' => 0, 'draft' => 0);
		foreach ($this->db->select('status, COUNT(*) AS n')->group_by('status')->get('posts')->result_array() as $r)
		{
			$out[$r['status']] = (int) $r['n'];
		}

		return $out;
	}

	public function count_published_by_author($author_id)
	{
		return $this->db->where('author_id', $author_id)->where('status', 'publish')->count_all_results('posts');
	}
}
