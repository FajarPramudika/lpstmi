<?php
$files = array_merge(
	glob(__DIR__.'/application/views/layouts/partials/*.php'),
	glob(__DIR__.'/application/views/layouts/foot/*.php')
);

foreach ($files as $file) {
	$content = file_get_contents($file);
	
	// Check if any of the target strings exist before replacing
	if (strpos($content, 'humas@stmi.ac.id') !== false || strpos($content, '021-42888206') !== false || strpos($content, '0851-552-44455') !== false || strpos($content, '6285155244455') !== false) {
		
		$content = str_replace('humas@stmi.ac.id', '<?= html_escape($contacts[\'email\']) ?>', $content);
		$content = str_replace('021-42888206', '<?= html_escape($contacts[\'phone\']) ?>', $content);
		$content = str_replace('0851-552-44455', '<?= html_escape($contacts[\'whatsapp\']) ?>', $content);
		
		$content = str_replace('https:\/\/web.whatsapp.com\/send?phone=6285155244455', '<?= str_replace(\'/\', \'\\/\', html_escape($contacts[\'whatsapp_url\'])) ?>', $content);
		$content = str_replace('"value":"6285155244455"', '"value":"<?= html_escape(preg_replace(\'/[^0-9]/\', \'\', strpos($contacts[\'whatsapp\'], \'0\') === 0 ? \'62\' . substr($contacts[\'whatsapp\'], 1) : $contacts[\'whatsapp\'])) ?>" ', $content);
		// Note: The extra space above is a hack just in case, but let's just do exact string:
		// Actually let's do:
		$content = str_replace('"value":"<?= html_escape(preg_replace(\'/[^0-9]/\', \'\', strpos($contacts[\'whatsapp\'], \'0\') === 0 ? \'62\' . substr($contacts[\'whatsapp\'], 1) : $contacts[\'whatsapp\'])) ?>" ', '"value":"<?= html_escape(preg_replace(\'/[^0-9]/\', \'\', strpos($contacts[\'whatsapp\'], \'0\') === 0 ? \'62\' . substr($contacts[\'whatsapp\'], 1) : $contacts[\'whatsapp\'])) ?>"', $content);
		
		file_put_contents($file, $content);
		echo "Patched: " . basename($file) . "\n";
	}
}
echo "Done.\n";
