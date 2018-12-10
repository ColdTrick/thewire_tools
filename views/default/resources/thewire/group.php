<?php
/**
 * Display TheWire for a group
 */

$group_guid = (int) get_input('guid', 0);
elgg_entity_gatekeeper($group_guid, 'group');

$entities_only = (int) get_input('entities_only', 0);

/* @var $group ElggGroup */
$group = get_entity($group_guid);

// check if The Wire is enabled
elgg_group_tool_gatekeeper('thewire', $group_guid);

elgg_push_collection_breadcrumbs('object', 'thewire', $group);

// build page elements
$title_text = elgg_echo('thewire_tools:group:title');

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

$body = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => $content,
	'sidebar' => elgg_view('thewire/sidebar'),
	'filter_id' => 'thewire/group',
	'filter_value' => 'all',
]);

// Display page
echo elgg_view_page($title_text,$body);
