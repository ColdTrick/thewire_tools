<?php
/**
 * Display TheWire for a group
 */

$group_guid = (int) get_input('guid', 0);
elgg_entity_gatekeeper($group_guid, 'group');

/* @var $group ElggGroup */
$group = get_entity($group_guid);

// check if The Wire is enabled
elgg_group_tool_gatekeeper('thewire', $group_guid);

elgg_push_collection_breadcrumbs('object', 'thewire', $group);

$content = '';
if ($group->isMember()) {
	$content .= elgg_view_form('thewire/add');
}

$content .= elgg_list_entities([
	'types' => 'object',
	'subtypes' => 'thewire',
	'pagination' => true,
	'container_guid' => $group->guid,
	'no_results' => elgg_echo('notfound'),
]);

// Display page
echo elgg_view_page(elgg_echo('thewire_tools:group:title'), [
	'content' => $content,
	'sidebar' => elgg_view('thewire/sidebar'),
	'filter_id' => 'thewire/group',
	'filter_value' => 'all',
]);
