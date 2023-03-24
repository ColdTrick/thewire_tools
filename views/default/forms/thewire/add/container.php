<?php

$entity = elgg_extract('entity', $vars);
if ($entity instanceof \ElggEntity) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'container_guid',
		'value' => $entity->container_guid,
	]);
	return;
}

if (elgg_get_plugin_setting('enable_group', 'thewire_tools') !== 'yes') {
	return;
}

$page_owner_entity = elgg_get_page_owner_entity();

if ($page_owner_entity instanceof \ElggGroup) {
	// in a group only allow sharing in the current group
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'container_guid',
		'value' => $page_owner_entity->guid,
	]);
	return;
}

$user_guid = elgg_get_logged_in_user_guid();
if (!$user_guid) {
	return;
}

$options_values = [
	$user_guid => elgg_echo('thewire_tools:add:container:site'),
];

$groups = elgg_get_entities([
	'type' => 'group',
	'limit' => false,
	'batch' => true,
	'relationship' => 'member',
	'relationship_guid' => $user_guid,
	'sort_by' => [
		'property' => 'name',
		'direction' => 'ASC',
	],
]);
/* @var $group ElggGroup */
foreach ($groups as $group) {
	if (!$group->isToolEnabled('thewire')) {
		continue;
	}
	
	$options_values[$group->guid] = $group->getDisplayName();
}

if (count($options_values) < 2) {
	return;
}

echo elgg_view_field([
	'#type' => 'select',
	'name' => 'container_guid',
	'options_values' => $options_values,
]);
