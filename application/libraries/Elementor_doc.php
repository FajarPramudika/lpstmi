<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Editor blok generik untuk konten Elementor (halaman/post hasil impor WordPress).
 *
 * HTML tidak pernah diserialisasi ulang: parser hanya mencatat posisi byte setiap tag, lalu perubahan diterapkan
 * sebagai penggantian potongan teks. Bagian yang tidak diubah tetap byte-identik dengan aslinya.
 *
 * - outline()     : pohon blok (kontainer, widget, tab, item akordeon) beserta field yang bisa diedit.
 * - apply_fields(): ganti isi field yang berubah (teks, judul, gambar, HTML, link ikon sosial).
 * - operate()     : duplikat / hapus / naik / turun satu blok; ID tab & akordeon dinomori ulang seperti Elementor.
 *
 * Kunci field & blok berisi posisi byte, jadi hanya berlaku untuk konten yang sama persis (cek hash()).
 */
class Elementor_doc {

	protected static $void = array('area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr');

	/** Label widget untuk editor. */
	public static $labels = array(
		'container'        => 'Kontainer',
		'text-editor'      => 'Teks',
		'heading'          => 'Judul',
		'image'            => 'Gambar',
		'html'             => 'HTML',
		'icon-list'        => 'Daftar ikon',
		'social-icons'     => 'Ikon media sosial',
		'nested-tabs'      => 'Tab',
		'nested-accordion' => 'Akordeon',
		'spacer'           => 'Spasi',
		'form'             => 'Form',
	);

	/** @var string */
	protected $html;
	/** @var array elemen: name, start, open_end, close_start, end, attrs, parent, children */
	protected $els = array();
	/** @var array blok Elementor: el, kind, widget, children, parent */
	protected $nodes = array();
	/** @var array indeks elemen -> indeks blok */
	protected $el_node = array();

	public function __construct($html = '')
	{
		if (is_string($html))
		{
			$this->load($html);
		}
	}

	public static function from($html)
	{
		return new self($html);
	}

	public static function hash($html)
	{
		return md5($html);
	}

	public function load($html)
	{
		$this->html = $html;
		$this->els = $this->nodes = $this->el_node = array();
		$this->parse();
		$this->build();

		return $this;
	}

	public function html()
	{
		return $this->html;
	}

	/* ------------------------------------------------------------------
	 * Parser
	 * ------------------------------------------------------------------ */

	protected function parse()
	{
		preg_match_all('~<!--.*?-->|<(script|style|textarea)\b[^>]*>.*?</\1\s*>|</([a-zA-Z][\w:-]*)\s*>|<([a-zA-Z][\w:-]*)((?:[^>"\']|"[^"]*"|\'[^\']*\')*)>~s',
			$this->html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

		$stack = array();
		foreach ($matches as $x)
		{
			$full = $x[0][0];
			$pos = $x[0][1];
			$len = strlen($full);
			$top = $stack ? end($stack) : NULL;

			if (isset($x[1]) && $x[1][1] >= 0 && $x[1][0] !== '')
			{
				// script/style/textarea: satu elemen utuh.
				$open_len = strpos($full, '>') + 1;
				$name = strtolower($x[1][0]);
				$this->add_element($name, $pos, $pos + $open_len, $pos + $len - strlen('</'.$name.'>'), $pos + $len,
					substr($full, strlen($name) + 1, $open_len - strlen($name) - 2), $top);
			}
			elseif (isset($x[2]) && $x[2][1] >= 0 && $x[2][0] !== '')
			{
				$name = strtolower($x[2][0]);
				for ($i = count($stack) - 1; $i >= 0; $i--)
				{
					if ($this->els[$stack[$i]]['name'] === $name)
					{
						$this->els[$stack[$i]]['close_start'] = $pos;
						$this->els[$stack[$i]]['end'] = $pos + $len;
						// Elemen yang belum ditutup di dalamnya dianggap selesai di sini.
						for ($j = count($stack) - 1; $j > $i; $j--)
						{
							$this->els[$stack[$j]]['close_start'] = $this->els[$stack[$j]]['end'] = $pos;
						}
						$stack = array_slice($stack, 0, $i);
						break;
					}
				}
			}
			elseif (isset($x[3]) && $x[3][1] >= 0)
			{
				$name = strtolower($x[3][0]);
				$attrs = isset($x[4]) ? $x[4][0] : '';
				$self = (in_array($name, self::$void, TRUE) || substr(rtrim($attrs), -1) === '/');
				$i = $this->add_element($name, $pos, $pos + $len, $self ? $pos + $len : NULL, $self ? $pos + $len : NULL, $attrs, $top);
				if ( ! $self)
				{
					$stack[] = $i;
				}
			}
		}

		foreach ($stack as $i)
		{
			$this->els[$i]['close_start'] = $this->els[$i]['end'] = strlen($this->html);
		}
	}

	protected function add_element($name, $start, $open_end, $close_start, $end, $attrs, $parent)
	{
		$i = count($this->els);
		$this->els[] = array('name' => $name, 'start' => $start, 'open_end' => $open_end, 'close_start' => $close_start,
			'end' => $end, 'attrs' => $attrs, 'parent' => $parent, 'children' => array());
		if ($parent !== NULL)
		{
			$this->els[$parent]['children'][] = $i;
		}

		return $i;
	}

	public static function attr($attrs, $name)
	{
		return preg_match('/(?:^|\s)'.preg_quote($name, '/').'\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $attrs, $m)
			? html_entity_decode(isset($m[2]) && $m[2] !== '' ? $m[2] : $m[1], ENT_QUOTES, 'UTF-8') : NULL;
	}

	protected function has_class($i, $class)
	{
		$c = self::attr($this->els[$i]['attrs'], 'class');

		return $c !== NULL && in_array($class, preg_split('/\s+/', trim($c)), TRUE);
	}

	/** Semua keturunan elemen $i (urutan dokumen). */
	protected function descendants($i)
	{
		$out = array();
		foreach ($this->els[$i]['children'] as $c)
		{
			$out[] = $c;
			$out = array_merge($out, $this->descendants($c));
		}

		return $out;
	}

	protected function find_desc($i, $name, $class = NULL)
	{
		foreach ($this->descendants($i) as $d)
		{
			if ($this->els[$d]['name'] === $name && ($class === NULL OR $this->has_class($d, $class)))
			{
				return $d;
			}
		}

		return NULL;
	}

	/** Blok Elementor terdekat yang menjadi leluhur elemen $i. */
	protected function owner($i)
	{
		for ($p = $this->els[$i]['parent']; $p !== NULL; $p = $this->els[$p]['parent'])
		{
			if (isset($this->el_node[$p]))
			{
				return $this->el_node[$p];
			}
		}

		return NULL;
	}

	protected function build()
	{
		foreach ($this->els as $i => $el)
		{
			$type = self::attr($el['attrs'], 'data-element_type');
			if ($type !== 'container' && $type !== 'widget')
			{
				continue;
			}
			$widget = ($type === 'widget') ? preg_replace('/\.default$/', '', (string) self::attr($el['attrs'], 'data-widget_type')) : NULL;
			$this->el_node[$i] = count($this->nodes);
			$this->nodes[] = array('el' => $i, 'kind' => $type, 'widget' => $widget, 'children' => array(), 'parent' => NULL);
		}
		foreach ($this->nodes as $n => $node)
		{
			$owner = $this->owner($node['el']);
			$this->nodes[$n]['parent'] = $owner;
			if ($owner !== NULL)
			{
				$this->nodes[$owner]['children'][] = $n;
			}
		}
	}

	/* ------------------------------------------------------------------
	 * Outline & field
	 * ------------------------------------------------------------------ */

	/**
	 * Pohon blok tingkat atas untuk editor. Setiap blok: key, kind, widget, label, fields, children,
	 * dan untuk tab/akordeon: items (title field + children).
	 */
	public function outline()
	{
		$out = array();
		foreach ($this->nodes as $n => $node)
		{
			if ($node['parent'] === NULL)
			{
				$out[] = $this->describe($n);
			}
		}

		return $out;
	}

	/** Kunci operasi blok = nomor urut blok di dokumen (tetap sama setelah isi field diubah). */
	protected function node_key($n)
	{
		return 'el:'.$n;
	}

	protected function describe($n)
	{
		$node = $this->nodes[$n];
		$w = $node['widget'];
		$block = array(
			'key'      => $this->node_key($n),
			'kind'     => $node['kind'],
			'widget'   => $w,
			'label'    => isset(self::$labels[$node['kind'] === 'container' ? 'container' : $w]) ? self::$labels[$node['kind'] === 'container' ? 'container' : $w] : $w,
			'fields'   => $this->fields($n),
			'children' => array(),
			'items'    => NULL,
		);

		if ($w === 'nested-tabs' OR $w === 'nested-accordion')
		{
			$block['items'] = array();
			foreach ($this->items($n) as $k => $item)
			{
				$block['items'][] = array(
					'key'      => 'item:'.$n.':'.$k,
					'title'    => $this->text_field($item['title_el'], $w === 'nested-tabs' ? 'Judul tab' : 'Judul item'),
					'children' => array_map(array($this, 'describe'), $item['nodes']),
				);
			}
		}
		else
		{
			$block['children'] = array_map(array($this, 'describe'), $node['children']);
		}

		return $block;
	}

	/**
	 * Tab / item akordeon milik widget $n: [key, title_el, nodes (blok isi), ranges].
	 * key tab = rentang tombol judul + rentang kontainer isi; key akordeon = rentang <details>.
	 */
	protected function items($n)
	{
		$node = $this->nodes[$n];
		$widget_el = $node['el'];
		$items = array();

		if ($node['widget'] === 'nested-tabs')
		{
			$titles = array();
			foreach ($this->descendants($widget_el) as $d)
			{
				if ($this->els[$d]['name'] === 'button' && $this->has_class($d, 'e-n-tab-title') && $this->owner($d) === $n)
				{
					$titles[] = $d;
				}
			}
			$contents = array_values(array_filter($node['children'], function ($c) {
				return strpos((string) self::attr($this->els[$this->nodes[$c]['el']]['attrs'], 'id'), 'e-n-tab-content-') === 0;
			}));
			foreach ($titles as $k => $t)
			{
				$c = isset($contents[$k]) ? $contents[$k] : NULL;
				$span = $this->find_desc($t, 'span', 'e-n-tab-title-text');
				$ce = ($c !== NULL) ? $this->els[$this->nodes[$c]['el']] : NULL;
				$items[] = array(
					'key'      => 'tab:'.$this->els[$t]['start'].':'.$this->els[$t]['end'].':'.($ce ? $ce['start'].':'.$ce['end'] : '0:0'),
					'title_el' => $span !== NULL ? $span : $t,
					'nodes'    => ($c !== NULL) ? array($c) : array(),
				);
			}
		}
		else
		{
			foreach ($this->descendants($widget_el) as $d)
			{
				if ($this->els[$d]['name'] === 'details' && $this->has_class($d, 'e-n-accordion-item') && $this->owner($d) === $n)
				{
					$title = $this->find_desc($d, 'div', 'e-n-accordion-item-title-text');
					$nodes = array();
					foreach ($node['children'] as $c)
					{
						$ce = $this->els[$this->nodes[$c]['el']];
						if ($ce['start'] >= $this->els[$d]['start'] && $ce['end'] <= $this->els[$d]['end'])
						{
							$nodes[] = $c;
						}
					}
					$items[] = array(
						'key'      => 'acc:'.$this->els[$d]['start'].':'.$this->els[$d]['end'],
						'title_el' => $title !== NULL ? $title : $d,
						'nodes'    => $nodes,
					);
				}
			}
		}

		return $items;
	}

	/** Isi elemen tanpa whitespace di tepi (whitespace aslinya dipertahankan saat disimpan). */
	protected function inner_field($i, $type, $label)
	{
		$el = $this->els[$i];
		$raw = substr($this->html, $el['open_end'], $el['close_start'] - $el['open_end']);

		return array('key' => $type.':'.$el['open_end'].':'.$el['close_start'], 'type' => $type, 'label' => $label, 'value' => trim($raw));
	}

	protected function text_field($i, $label)
	{
		return $this->inner_field($i, 'text', $label);
	}

	protected function fields($n)
	{
		$node = $this->nodes[$n];
		if ($node['kind'] !== 'widget')
		{
			return array();
		}
		$i = $node['el'];
		$box = $this->find_desc($i, 'div', 'elementor-widget-container');
		$box = ($box !== NULL) ? $box : $i;
		$fields = array();

		switch ($node['widget'])
		{
			case 'text-editor':
				$fields[] = $this->inner_field($box, 'rich', 'Teks');
				break;
			case 'html':
				$fields[] = $this->inner_field($box, 'code', 'HTML');
				break;
			case 'heading':
				$h = NULL;
				foreach ($this->descendants($i) as $d)
				{
					if ($this->has_class($d, 'elementor-heading-title'))
					{
						$h = $d;
						break;
					}
				}
				if ($h !== NULL)
				{
					$fields[] = $this->text_field($h, 'Judul');
				}
				break;
			case 'image':
				$img = $this->find_desc($i, 'img');
				if ($img !== NULL)
				{
					$f = $this->inner_field($box, 'image', 'Gambar');
					$attrs = $this->els[$img]['attrs'];
					$f['src'] = self::attr($attrs, 'src');
					$f['alt'] = self::attr($attrs, 'alt');
					$f['media_id'] = preg_match('/\bwp-image-(\d+)\b/', (string) self::attr($attrs, 'class'), $m) ? (int) $m[1] : NULL;
					$fields[] = $f;
				}
				break;
			case 'icon-list':
				$k = 0;
				foreach ($this->descendants($i) as $d)
				{
					if ($this->els[$d]['name'] === 'span' && $this->has_class($d, 'elementor-icon-list-text'))
					{
						$fields[] = $this->text_field($d, 'Item '.(++$k));
					}
				}
				break;
			case 'social-icons':
				foreach ($this->descendants($i) as $d)
				{
					if ($this->els[$d]['name'] === 'a' && $this->has_class($d, 'elementor-social-icon'))
					{
						$el = $this->els[$d];
						$name = $this->find_desc($d, 'span', 'elementor-screen-only');
						$fields[] = array(
							'key'   => 'href:'.$el['start'].':'.$el['open_end'],
							'type'  => 'href',
							'label' => 'Link '.($name !== NULL ? trim(strip_tags(substr($this->html, $this->els[$name]['open_end'], $this->els[$name]['close_start'] - $this->els[$name]['open_end']))) : ''),
							'value' => (string) self::attr($el['attrs'], 'href'),
						);
					}
				}
				break;
		}

		return $fields;
	}

	/** Semua field (termasuk judul tab/akordeon) berindeks key, untuk validasi saat menyimpan. */
	public function all_fields()
	{
		$out = array();
		$walk = function (array $blocks) use (&$walk, &$out) {
			foreach ($blocks as $b)
			{
				foreach ($b['fields'] as $f)
				{
					$out[$f['key']] = $f;
				}
				if ($b['items'] !== NULL)
				{
					foreach ($b['items'] as $it)
					{
						$out[$it['title']['key']] = $it['title'];
						$walk($it['children']);
					}
				}
				$walk($b['children']);
			}
		};
		$walk($this->outline());

		return $out;
	}

	/* ------------------------------------------------------------------
	 * Menyimpan field
	 * ------------------------------------------------------------------ */

	/**
	 * $values: key => nilai baru dari form. $image_builder: callable(field, media_id) -> HTML baru isi widget gambar
	 * (NULL = abaikan). Hanya field yang nilainya berubah yang diganti. Mengembalikan jumlah field yang diubah.
	 */
	public function apply_fields(array $values, callable $image_builder)
	{
		$fields = $this->all_fields();
		$edits = array();

		foreach ($values as $key => $value)
		{
			if ( ! isset($fields[$key]))
			{
				continue;
			}
			$f = $fields[$key];
			$value = str_replace("\r\n", "\n", (string) $value);
			list($type, $start, $end) = explode(':', $key);
			$start = (int) $start;
			$end = (int) $end;

			if ($type === 'image')
			{
				$media_id = (int) $value;
				if ( ! $media_id OR $media_id === (int) $f['media_id'])
				{
					continue;
				}
				$new = $image_builder($f, $media_id);
				if ($new === NULL)
				{
					continue;
				}
				$edits[] = array($start, $end, $this->keep_ws(substr($this->html, $start, $end - $start), $new));
			}
			elseif ($type === 'href')
			{
				if ($value === $f['value'])
				{
					continue;
				}
				$tag = substr($this->html, $start, $end - $start);
				$attr = ($value === '') ? '' : ' href="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"';
				$tag = preg_match('/\shref\s*=\s*("[^"]*"|\'[^\']*\')/', $tag)
					? preg_replace('/\shref\s*=\s*("[^"]*"|\'[^\']*\')/', $attr, $tag, 1)
					: preg_replace('/^<a\b/', '<a'.$attr, $tag);
				$edits[] = array($start, $end, $tag);
			}
			else
			{
				if ($value === str_replace("\r\n", "\n", $f['value']))
				{
					continue;
				}
				$edits[] = array($start, $end, $this->keep_ws(substr($this->html, $start, $end - $start), $value));
			}
		}

		$this->splice($edits);

		return count($edits);
	}

	/** Pertahankan whitespace di tepi isi asli. */
	protected function keep_ws($old, $new)
	{
		preg_match('/^\s*/', $old, $a);
		preg_match('/\s*$/', $old, $b);

		return $a[0].trim($new).$b[0];
	}

	/** Terapkan penggantian [start, end, teks] (tidak tumpang tindih) dari belakang, lalu parse ulang. */
	protected function splice(array $edits)
	{
		usort($edits, function ($a, $b) { return $b[0] - $a[0]; });
		$html = $this->html;
		foreach ($edits as $e)
		{
			$html = substr($html, 0, $e[0]).$e[2].substr($html, $e[1]);
		}
		$this->load($html);
	}

	/* ------------------------------------------------------------------
	 * Operasi struktur
	 * ------------------------------------------------------------------ */

	/**
	 * $op: dup | del | up | down, $key: kunci blok dari outline() ("el:<n>" atau "item:<n>:<k>").
	 * Mengembalikan pesan error atau NULL jika berhasil.
	 */
	public function operate($op, $key)
	{
		// Cari target & saudara-saudaranya (tiap saudara = daftar rentang byte).
		list($siblings, $index, $widget_n) = $this->siblings($key);
		if ($siblings === NULL)
		{
			return 'Blok tidak ditemukan. Muat ulang halaman editor.';
		}
		$count = count($siblings);
		$ranges = $siblings[$index];

		if ($op === 'del')
		{
			if ($count < 2 && $widget_n !== NULL)
			{
				return 'Tab/item terakhir tidak bisa dihapus.';
			}
			$edits = array();
			foreach ($ranges as $r)
			{
				$edits[] = array($r[0] - strlen($this->ws_before($r[0])), $r[1], '');
			}
			$this->splice($edits);
		}
		elseif ($op === 'dup')
		{
			$edits = array();
			foreach ($ranges as $r)
			{
				$edits[] = array($r[1], $r[1], $this->ws_before($r[0]).$this->reid(substr($this->html, $r[0], $r[1] - $r[0])));
			}
			$this->splice($edits);
		}
		elseif ($op === 'up' OR $op === 'down')
		{
			$other = ($op === 'up') ? $index - 1 : $index + 1;
			if ($other < 0 OR $other >= $count)
			{
				return NULL;
			}
			$a = $siblings[min($index, $other)];
			$b = $siblings[max($index, $other)];
			$edits = array();
			foreach ($a as $k => $ra)
			{
				$rb = $b[$k];
				$edits[] = array($ra[0], $ra[1], substr($this->html, $rb[0], $rb[1] - $rb[0]));
				$edits[] = array($rb[0], $rb[1], substr($this->html, $ra[0], $ra[1] - $ra[0]));
			}
			$this->splice($edits);
		}
		else
		{
			return 'Operasi tidak dikenal.';
		}

		if ($widget_n !== NULL)
		{
			$this->renumber($widget_n);
		}

		return NULL;
	}

	/**
	 * $key "el:<n>" (blok) atau "item:<n>:<k>" (tab/item akordeon ke-k milik widget n).
	 * Mengembalikan [saudara (tiap saudara = daftar rentang), indeks target, widget n untuk penomoran ulang / NULL].
	 */
	protected function siblings($key)
	{
		$p = explode(':', $key);
		if ($p[0] === 'el' && isset($p[1], $this->nodes[(int) $p[1]]))
		{
			$n = (int) $p[1];
			$node = $this->nodes[$n];
			$group = array();
			foreach ($this->nodes as $m => $other)
			{
				if ($other['parent'] === $node['parent'] && $this->els[$other['el']]['parent'] === $this->els[$node['el']]['parent'])
				{
					$group[$m] = array(array($this->els[$other['el']]['start'], $this->els[$other['el']]['end']));
				}
			}

			return array(array_values($group), array_search($n, array_keys($group), TRUE), NULL);
		}

		if ($p[0] === 'item' && isset($p[1], $p[2], $this->nodes[(int) $p[1]])
			&& in_array($this->nodes[(int) $p[1]]['widget'], array('nested-tabs', 'nested-accordion'), TRUE))
		{
			$n = (int) $p[1];
			$group = array();
			foreach ($this->items($n) as $item)
			{
				$r = explode(':', $item['key']);
				$group[] = ($r[0] === 'tab')
					? array_values(array_filter(array(array((int) $r[1], (int) $r[2]), array((int) $r[3], (int) $r[4])), function ($x) { return $x[1] > $x[0]; }))
					: array(array((int) $r[1], (int) $r[2]));
			}
			if (isset($group[(int) $p[2]]))
			{
				return array($group, (int) $p[2], $n);
			}
		}

		return array(NULL, NULL, NULL);
	}

	/** Whitespace tepat sebelum posisi $pos (dipakai sebagai pemisah saat duplikat/hapus). */
	protected function ws_before($pos)
	{
		preg_match('/[ \t\r\n]*$/', substr($this->html, max(0, $pos - 200), min(200, $pos)), $m);

		return $m[0];
	}

	/**
	 * Salinan blok mendapat nomor widget tab/akordeon baru, supaya ID di halaman tetap unik.
	 */
	protected function reid($html)
	{
		if (preg_match_all('/data-widget-number="(\d+)"/', $html, $m))
		{
			foreach (array_unique($m[1]) as $old)
			{
				$new = (string) mt_rand(100000000, 999999999);
				$html = preg_replace('/(data-widget-number="|e-n-tab-(?:title|content)-)'.$old.'/', '${1}'.$new, $html);
			}
		}
		if (preg_match_all('/id="e-n-accordion-item-(\d+)0"/', $html, $m))
		{
			foreach (array_unique($m[1]) as $old)
			{
				$new = (string) mt_rand(1000, 9999);
				$html = preg_replace('/e-n-accordion-item-'.$old.'(?=\d)/', 'e-n-accordion-item-'.$new, $html);
			}
		}

		return $html;
	}

	/**
	 * Nomori ulang tab / item akordeon milik widget ke-$n (urutan blok tidak berubah oleh operasi di dalamnya), seperti output Elementor:
	 * item pertama aktif/terbuka, indeks 1..n, ID = nomor widget + indeks.
	 */
	protected function renumber($n)
	{
		if ( ! isset($this->nodes[$n]))
		{
			return;
		}
		$items = $this->items($n);
		$edits = array();

		if ($this->nodes[$n]['widget'] === 'nested-tabs')
		{
			$tabs_el = $this->find_desc($this->nodes[$n]['el'], 'div', 'e-n-tabs');
			$wn = self::attr($this->els[$tabs_el]['attrs'], 'data-widget-number');
			foreach ($items as $k => $item)
			{
				$i = $k + 1;
				$p = explode(':', $item['key']);
				$title = substr($this->html, (int) $p[1], (int) $p[2] - (int) $p[1]);
				$title = preg_replace('/id="e-n-tab-title-\d+"/', 'id="e-n-tab-title-'.$wn.$i.'"', $title, 1);
				$title = preg_replace('/aria-selected="[^"]*"/', 'aria-selected="'.($i === 1 ? 'true' : 'false').'"', $title, 1);
				$title = preg_replace('/data-tab-index="\d+"/', 'data-tab-index="'.$i.'"', $title, 1);
				$title = preg_replace('/tabindex="-?\d+"/', 'tabindex="'.($i === 1 ? '0' : '-1').'"', $title, 1);
				$title = preg_replace('/aria-controls="e-n-tab-content-\d+"/', 'aria-controls="e-n-tab-content-'.$wn.$i.'"', $title, 1);
				$title = preg_replace('/--n-tabs-title-order: \d+;/', '--n-tabs-title-order: '.$i.';', $title, 1);
				$edits[] = array((int) $p[1], (int) $p[2], $title);

				if ((int) $p[4] > (int) $p[3])
				{
					$start = (int) $p[3];
					$el_end = strpos($this->html, '>', $start) + 1;
					$open = substr($this->html, $start, $el_end - $start);
					$open = preg_replace('/(aria-labelledby="[^"]*?)e-n-tab-title-\d+/', '${1}e-n-tab-title-'.$wn.$i, $open, 1);
					$open = preg_replace('/id="e-n-tab-content-\d+"/', 'id="e-n-tab-content-'.$wn.$i.'"', $open, 1);
					$open = preg_replace('/data-tab-index="\d+"/', 'data-tab-index="'.$i.'"', $open, 1);
					$open = preg_replace('/--n-tabs-title-order: \d+;/', '--n-tabs-title-order: '.$i.';', $open, 1);
					$open = preg_replace('/class="(?:e-active)?\s?elementor-element/', 'class="'.($i === 1 ? 'e-active' : '').' elementor-element', $open, 1);
					$edits[] = array($start, $el_end, $open);
				}
			}
		}
		else
		{
			$prefix = NULL;
			foreach ($items as $k => $item)
			{
				$p = explode(':', $item['key']);
				$start = (int) $p[1];
				$end = (int) $p[2];
				$block = substr($this->html, $start, $end - $start);
				if ( ! preg_match('/^<details id="e-n-accordion-item-(\d+)"/', $block, $m))
				{
					continue;
				}
				if ($prefix === NULL)
				{
					// ID item pertama = prefix + "0".
					$prefix = substr($m[1], 0, -1);
				}
				$old = $m[1];
				$new = $prefix.$k;
				$block = preg_replace('/e-n-accordion-item-'.$old.'(?!\d)/', 'e-n-accordion-item-'.$new, $block);
				// Elementor: item pertama <details ... open>, lainnya <details ... >.
				$block = preg_replace('/^(<details [^>]*?) open>/', '$1 >', $block, 1);
				if ($k === 0)
				{
					$block = preg_replace('/^(<details [^>]*?) >/', '$1 open>', $block, 1);
				}
				$block = preg_replace('/data-accordion-index="\d+"/', 'data-accordion-index="'.($k + 1).'"', $block, 1);
				$block = preg_replace('/aria-expanded="[^"]*"/', 'aria-expanded="'.($k === 0 ? 'true' : 'false').'"', $block, 1);
				$edits[] = array($start, $end, $block);
			}
		}

		$this->splice($edits);
	}
}
