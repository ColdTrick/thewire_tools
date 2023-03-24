<?php

$page_owner = elgg_get_page_owner_entity();

$options = [
	'type' => 'object',
	'subtype' => 'thewire',
	'metadata_name' => 'featured',
	'limit' => 5,
	'item_view' => 'object/thewire/featured',
];

if ($page_owner instanceof \ElggUser) {
	$options['owner_guid'] = $page_owner->guid;
} elseif ($page_owner instanceof \ElggGroup) {
	$options['container_guid'] = $page_owner->guid;
}

$count = elgg_count_entities($options);
if (empty($count)) {
	return;
}

echo elgg_view_module('aside', elgg_echo('thewire_tools:sidebar:featured'), elgg_list_entities($options));
