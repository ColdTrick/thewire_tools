<?php
/**
 * Ajax view to show a list of users who shared an item on TheWire
 *
 */

use Elgg\BadRequestException;

$guid = (int) get_input('entity_guid');
if (empty($guid)) {
	throw new BadRequestException();
}

$batch = elgg_get_entities([
	'type' => 'object',
	'subtype' => 'thewire',
	'relationship_guid' => $guid,
	'relationship' => 'reshare',
	'inverse_relationship' => true,
	'limit' => false,
	'batch' => true,
]);

$list_items = [];
/* @var $wire_post ElggWire */
foreach ($batch as $wire_post) {
	$owner = $wire_post->getOwnerEntity();
	$icon = elgg_view_entity_icon($owner, 'tiny', ['use_hover' => false]);
	
	$owner_link = elgg_view('output/url', [
		'text' => $owner->getDisplayName(),
		'href' => $owner->getURL(),
		'is_trusted' => true,
	]);
	
	$friendly_time = elgg_view_friendly_time($wire_post->time_created);
	
	$container = $wire_post->getContainerEntity();
	if ($container instanceof \ElggGroup) {
		$container_link = elgg_view('output/url', [
			'text' => $container->getDisplayName(),
			'href' => $container->getURL(),
			'is_trusted' => true,
		]);
	
		$text = elgg_echo('thewire_tools:reshare:list:group', [$owner_link, $container_link, $friendly_time]);
	} else {
		$text = elgg_echo('thewire_tools:reshare:list', [$owner_link, $friendly_time]);
	}
	
	$block = elgg_view_image_block($icon, $text);
	
	$list_items[] = elgg_format_element('li', ['class' => 'elgg-item'], $block);
}

if (empty($list_items)) {
	return;
}

echo elgg_format_element('ul', ['class' => 'elgg-list thewire-tools-reshare-popup'], implode(PHP_EOL, $list_items));
