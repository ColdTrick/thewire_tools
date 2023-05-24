<?php
/**
 * Show to what a wire post is linked
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggObject) {
	return;
}

$text = $entity->getDisplayName();
if (elgg_is_empty($text)) {
	$text = elgg_get_excerpt((string) $entity->description, 140);
}

if (elgg_is_empty($text)) {
	// no text to display
	return;
}

$icon = '';
if ($entity->hasIcon('tiny')) {
	$icon = elgg_view_entity_icon($entity, 'tiny');
}

$url = $entity->getURL();

$content = elgg_echo('thewire_tools:reshare:source') . ': ';
if (!empty($url)) {
	$content .= elgg_view('output/url', [
		'href' => $url,
		'text' => $text,
		'is_trusted' => true,
	]);
} else {
	$content .= elgg_view('output/text', ['value' => $text]);
}

$content = elgg_format_element('div', ['class' => 'elgg-subtext'], $content);

echo elgg_view_image_block($icon, $content, ['class' => 'mbn']);
