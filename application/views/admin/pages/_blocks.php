<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Editor blok Elementor (rekursif). $blocks dari Elementor_doc::outline().
 * Tombol aksi mengirim form utama dengan block_op = "<op>|<key>" (perubahan field ikut tersimpan).
 */
$actions = function ($key, $del_confirm) {
	$b = function ($op, $label, $title, $extra = '') use ($key) {
		return '<button type="submit" class="btn btn-sm btn-icon" name="block_op" value="'.html_escape($op.'|'.$key).'" title="'.$title.'" aria-label="'.$title.'" formnovalidate'.$extra.'>'.$label.'</button>';
	};
	return '<span class="blk-actions">'.$b('up', '&uarr;', 'Pindah ke atas').$b('down', '&darr;', 'Pindah ke bawah')
		.$b('dup', '&#x29C9;', 'Duplikat').$b('del', '&times;', 'Hapus', ' data-confirm-click="'.html_escape($del_confirm).'"').'</span>';
};
$preview = function (array $b) {
	foreach ($b['fields'] as $f)
	{
		if ($f['type'] !== 'image' && $f['type'] !== 'href')
		{
			$t = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags(preg_replace('/\[wpdm_package[^\]]*\]/', '[download]', $f['value'])), ENT_QUOTES, 'UTF-8')));
			return mb_strlen($t) > 70 ? mb_substr($t, 0, 70).'…' : $t;
		}
	}
	return '';
};
foreach ($blocks as $b):
	$anchor = 'blk-'.str_replace(':', '-', $b['key']);
	$is_container = ($b['kind'] === 'container');
?>
<div class="blk <?= $is_container ? 'blk-container' : 'blk-widget' ?>" id="<?= $anchor ?>">
	<div class="blk-head">
		<span class="blk-label"><?= html_escape($b['label']) ?></span>
		<span class="blk-preview muted"><?= html_escape($is_container ? '' : $preview($b)) ?></span>
		<?= $actions($b['key'], $is_container ? 'Hapus kontainer ini beserta seluruh isinya?' : 'Hapus blok '.$b['label'].' ini?') ?>
	</div>
	<?php if ($b['fields']): ?>
	<div class="blk-fields">
		<?php foreach ($b['fields'] as $f): $name = 'blocks['.$f['key'].']'; ?>
		<?php if ($f['type'] === 'rich'): ?>
		<div class="field">
			<textarea name="<?= html_escape($name) ?>" class="code blk-rich" rows="6"><?= html_escape(wp_content($f['value'])) ?></textarea>
			<button type="button" class="btn btn-sm" data-rich-open>Editor visual</button>
			<button type="button" class="btn btn-sm" data-download-insert>Sisipkan kartu download</button>
		</div>
		<?php elseif ($f['type'] === 'code'): ?>
		<div class="field"><label><?= html_escape($f['label']) ?></label><textarea name="<?= html_escape($name) ?>" class="code" rows="4"><?= html_escape(wp_content($f['value'])) ?></textarea></div>
		<?php elseif ($f['type'] === 'image'): ?>
		<div class="field blk-image" data-block-image>
			<input type="hidden" name="<?= html_escape($name) ?>" value="<?= (int) $f['media_id'] ?>">
			<img src="<?= html_escape(wp_content((string) $f['src'])) ?>" alt="">
			<div><button type="button" class="btn btn-sm" data-image-pick>Ganti gambar</button><div class="hint">Dari pustaka media; ukuran & srcset disesuaikan otomatis.</div></div>
		</div>
		<?php elseif ($f['type'] === 'href'): ?>
		<div class="field"><label><?= html_escape($f['label']) ?></label><input type="url" name="<?= html_escape($name) ?>" value="<?= html_escape(wp_content($f['value'])) ?>" placeholder="https://… (kosong = tanpa link)"></div>
		<?php else: ?>
		<div class="field"><label><?= html_escape($f['label']) ?></label><input type="text" name="<?= html_escape($name) ?>" value="<?= html_escape(wp_content($f['value'])) ?>"></div>
		<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<?php if ($b['items'] !== NULL): ?>
	<div class="blk-items">
		<?php foreach ($b['items'] as $k => $it): $t = $it['title']; ?>
		<details class="blk-item" id="blk-<?= str_replace(':', '-', $it['key']) ?>" <?= $k === 0 ? 'open' : '' ?>>
			<summary>
				<span class="blk-label"><?= $b['widget'] === 'nested-tabs' ? 'Tab' : 'Item' ?> <?= $k + 1 ?></span>
				<span class="blk-preview"><?= html_escape(html_entity_decode(strip_tags($t['value']), ENT_QUOTES, 'UTF-8')) ?></span>
				<?= $actions($it['key'], 'Hapus '.($b['widget'] === 'nested-tabs' ? 'tab' : 'item').' ini beserta isinya?') ?>
			</summary>
			<div class="field"><label><?= html_escape($t['label']) ?></label><input type="text" name="<?= html_escape('blocks['.$t['key'].']') ?>" value="<?= html_escape(wp_content($t['value'])) ?>"></div>
			<?php $this->load->view('admin/pages/_blocks', array('blocks' => $it['children'])); ?>
		</details>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<?php if ($b['children']): ?>
	<div class="blk-children"><?php $this->load->view('admin/pages/_blocks', array('blocks' => $b['children'])); ?></div>
	<?php endif; ?>
</div>
<?php endforeach; ?>
