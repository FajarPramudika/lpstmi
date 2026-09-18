<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kategori (category) dan tag (post_tag).
 */
class Term_model extends CI_Model {

	public function all($taxonomy)
	{
		return $this->db
			->select('t.*, (SELECT COUNT(*) FROM post_terms pt WHERE pt.term_id = t.id) AS post_count', FALSE)
			->from('terms t')
			->where('t.taxonomy', $taxonomy)
			->order_by('t.name')
			->get()
			->result_array();
	}

	public function find($id)
	{
		return $this->db->where('id', (int) $id)->get('terms')->row_array();
	}

	public function create(array $data)
	{
		$this->db->insert('terms', $data);

		return $this->db->insert_id();
	}

	public function update($id, array $data)
	{
		return $this->db->where('id', (int) $id)->update('terms', $data);
	}

	public function delete($id)
	{
		return $this->db->where('id', (int) $id)->delete('terms');
	}

	public function slug_taken($taxonomy, $slug, $except_id = NULL)
	{
		$this->db->where('taxonomy', $taxonomy)->where('slug', $slug);
		if ($except_id)
		{
			$this->db->where('id !=', (int) $except_id);
		}

		return $this->db->count_all_results('terms') > 0;
	}

	/**
	 * ID tag dari daftar nama; tag yang belum ada dibuat.
	 */
	public function tag_ids(array $names)
	{
		$ids = array();
		foreach ($names as $name)
		{
			$name = trim($name);
			if ($name === '')
			{
				continue;
			}
			$row = $this->db->where('taxonomy', 'post_tag')->where('name', $name)->get('terms')->row_array();
			if ( ! $row)
			{
				$slug = unique_slug(slugify($name), function ($s) {
					return $this->slug_taken('post_tag', $s);
				});
				$ids[] = $this->create(array('taxonomy' => 'post_tag', 'name' => $name, 'slug' => $slug, 'description' => ''));
			}
			else
			{
				$ids[] = (int) $row['id'];
			}
		}

		return array_values(array_unique($ids));
	}
}
