<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Menu utama situs (header desktop + menu mobile), bertingkat tanpa batas kedalaman.
 *
 * type:
 *   page     : halaman statis; slug = kunci URL ('' = beranda), object_id = ID page WordPress (kelas page-item-<ID>)
 *   category : arsip kategori; object_id = terms.id
 *   custom   : URL bebas (token {base_url} untuk situs ini); url NULL = label tanpa link
 */
class Menu_model extends CI_Model {

	protected $rows = NULL;

	/**
	 * Semua item, urut induk lalu posisi, beserta slug kategori.
	 */
	public function all()
	{
		if ($this->rows === NULL)
		{
			$this->rows = $this->db
				->select('m.*, t.slug AS term_slug, t.name AS term_name')
				->from('menu_items m')
				->join('terms t', "t.id = m.object_id AND m.type = 'category'", 'left')
				->order_by('m.parent_id IS NOT NULL', '', FALSE)
				->order_by('m.parent_id')
				->order_by('m.position')
				->order_by('m.id')
				->get()
				->result_array();
		}

		return $this->rows;
	}

	/**
	 * Pohon menu: item level atas, masing-masing dengan 'children'.
	 */
	public function tree()
	{
		$by_parent = array();
		foreach ($this->all() as $row)
		{
			$by_parent[(int) $row['parent_id']][] = $row;
		}

		$build = function ($parent_id) use (&$build, $by_parent) {
			$out = array();
			if (isset($by_parent[$parent_id]))
			{
				foreach ($by_parent[$parent_id] as $row)
				{
					$row['children'] = $build((int) $row['id']);
					$out[] = $row;
				}
			}

			return $out;
		};

		return $build(0);
	}

	/**
	 * Daftar datar untuk admin: urutan pohon, dengan 'depth'.
	 */
	public function flat()
	{
		$out = array();
		$walk = function (array $items, $depth) use (&$walk, &$out) {
			foreach ($items as $item)
			{
				$children = $item['children'];
				unset($item['children']);
				$item['depth'] = $depth;
				$item['has_children'] = ! empty($children);
				$out[] = $item;
				$walk($children, $depth + 1);
			}
		};
		$walk($this->tree(), 0);

		return $out;
	}

	/**
	 * URL tujuan item (NULL = tanpa link).
	 */
	public static function href(array $item)
	{
		switch ($item['type'])
		{
			case 'page':
				return site_url($item['slug']);
			case 'category':
				return ($item['term_slug'] !== NULL) ? site_url('category/'.$item['term_slug']) : '#';
			default:
				return ($item['url'] === NULL) ? NULL : wp_content($item['url']);
		}
	}

	/**
	 * Kelas menu aktif per ID item, seperti _wp_menu_item_classes_by_context() WordPress.
	 *
	 * $context:
	 *   array('page' => '<slug>')          halaman statis ('' = beranda)
	 *   array('category' => <term id>)     arsip kategori
	 *   array('post_categories' => [ids])  single post
	 *   array('url' => '<url>')            dicocokkan dengan item URL bebas
	 */
	public function active_for(array $context)
	{
		$rows = array();
		foreach ($this->all() as $row)
		{
			$rows[(int) $row['id']] = $row;
		}

		$current = array();
		$active = array();
		foreach ($rows as $id => $row)
		{
			if ($row['type'] === 'page' && isset($context['page']) && $row['slug'] === $context['page'])
			{
				$active[$id] = 'current-menu-item page_item page-item-'.$row['object_id'].' current_page_item';
				$current[] = $id;
			}
			elseif ($row['type'] === 'category' && isset($context['category']) && (int) $row['object_id'] === (int) $context['category'])
			{
				$active[$id] = 'current-menu-item';
				$current[] = $id;
			}
			elseif ($row['type'] === 'category' && isset($context['post_categories']) && in_array((int) $row['object_id'], array_map('intval', $context['post_categories']), TRUE))
			{
				// WordPress tidak menandai induk item ini untuk single post.
				$active[$id] = 'current-post-ancestor current-menu-parent current-post-parent';
			}
			elseif ($row['type'] === 'custom' && $row['url'] !== NULL && isset($context['url'])
				&& rtrim(wp_content($row['url']), '/') === rtrim($context['url'], '/'))
			{
				$active[$id] = 'current-menu-item';
				$current[] = $id;
			}
		}

		// Induk langsung: current-menu-ancestor current-menu-parent; induk di atasnya: current-menu-ancestor.
		foreach ($current as $id)
		{
			$parent = (int) $rows[$id]['parent_id'];
			$level = 0;
			while ($parent && isset($rows[$parent]))
			{
				if ( ! isset($active[$parent]))
				{
					$active[$parent] = ($level === 0) ? 'current-menu-ancestor current-menu-parent' : 'current-menu-ancestor';
				}
				$parent = (int) $rows[$parent]['parent_id'];
				$level++;
			}
		}

		return $active;
	}

	/* ------------------------------------------------------------------
	 * Panel admin
	 * ------------------------------------------------------------------ */

	public function find($id)
	{
		return $this->db->where('id', (int) $id)->get('menu_items')->row_array();
	}

	public function create(array $data)
	{
		$data['position'] = $this->next_position($data['parent_id']);
		$this->db->insert('menu_items', $data);

		return $this->db->insert_id();
	}

	/**
	 * Ubah item. Pindah induk = ditaruh di urutan terakhir induk baru.
	 */
	public function update($id, array $data)
	{
		$item = $this->find($id);
		if ((int) $item['parent_id'] !== (int) $data['parent_id'])
		{
			$data['position'] = $this->next_position($data['parent_id']);
		}
		$this->db->where('id', (int) $id)->update('menu_items', $data);
		$this->renumber($item['parent_id']);
	}

	/**
	 * Hapus item beserta semua sub-itemnya (FK ON DELETE CASCADE).
	 */
	public function delete($id)
	{
		$item = $this->find($id);
		$this->db->where('id', (int) $id)->delete('menu_items');
		$this->renumber($item['parent_id']);
	}

	/**
	 * Geser item satu langkah ke atas (-1) atau ke bawah (+1) di antara saudaranya.
	 */
	public function move($id, $step)
	{
		$item = $this->find($id);
		$siblings = $this->siblings($item['parent_id']);
		$ids = array_map('intval', array_column($siblings, 'id'));
		$i = array_search((int) $id, $ids, TRUE);
		$j = $i + $step;
		if ($i === FALSE OR $j < 0 OR $j >= count($ids))
		{
			return;
		}
		list($ids[$i], $ids[$j]) = array($ids[$j], $ids[$i]);
		$this->save_order($ids);
	}

	/**
	 * ID item dan semua turunannya (untuk mencegah item dipindah ke bawah dirinya sendiri).
	 */
	public function descendant_ids($id)
	{
		$ids = array((int) $id);
		for ($i = 0; $i < count($ids); $i++)
		{
			foreach ($this->db->select('id')->where('parent_id', $ids[$i])->get('menu_items')->result_array() as $row)
			{
				$ids[] = (int) $row['id'];
			}
		}

		return $ids;
	}

	protected function siblings($parent_id)
	{
		$parent_id ? $this->db->where('parent_id', (int) $parent_id) : $this->db->where('parent_id IS NULL', NULL, FALSE);

		return $this->db->order_by('position')->order_by('id')->get('menu_items')->result_array();
	}

	protected function next_position($parent_id)
	{
		$siblings = $this->siblings($parent_id);

		return empty($siblings) ? 1 : (int) end($siblings)['position'] + 1;
	}

	protected function renumber($parent_id)
	{
		$this->save_order(array_map('intval', array_column($this->siblings($parent_id), 'id')));
	}

	protected function save_order(array $ids)
	{
		foreach ($ids as $i => $id)
		{
			$this->db->where('id', $id)->update('menu_items', array('position' => $i + 1));
		}
	}
}
