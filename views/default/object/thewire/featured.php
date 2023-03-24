<?php
/**
 * Show a featured listing of a Wire post (in the sidebar)
 *
 * @uses $vars['entity'] the post to show
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggWire) {
	return;
}

$content = thewire_filter($entity->description);
$content .= elgg_view('output/url', [
	'text' => elgg_echo('more'),
	'href' => elgg_generate_url('collection:object:thewire:thread', [
		'guid' => $entity->wire_thread,
	]) . "#elgg-object-{$entity->guid}",
	'class' => 'mls',
]);

$params = [
	'entity' => $entity,
	'title' => false,
	'access' => false,
	'show_entity_menu' => false,
	'show_social_menu' => false,
	'icon_size' => 'tiny',
	'icon_entity' => $entity->getOwnerEntity(),
	'content' => $content,
];
$params = $params + $vars;

echo elgg_view('object/elements/summary', $params);
