<?php

$entity = elgg_extract('entity', $vars);
if ($entity) {
	echo elgg_view('input/hidden', [
		'name' => 'container_guid',
		'value' => $entity->container_guid,
	]);
	return;
}

if (!thewire_tools_groups_enabled()) {
	return;
}

$page_owner_entity = elgg_get_page_owner_entity();

if ($page_owner_entity instanceof ElggGroup) {
	// in a group only allow sharing in the current group
	echo elgg_view('input/hidden', [
		'name' => 'container_guid',
		'value' => $page_owner_entity->guid,
	]);
	return;
}

$user_guid = elgg_get_logged_in_user_guid();
if (!$user_guid) {
	return;
}

$options_values = ['' => elgg_echo('thewire_tools:add:container:site')];

$groups = new \ElggBatch('elgg_get_entities_from_relationship', [
	'type' => 'group',
	'limit' => false,
	'relationship' => 'member',
	'relationship_guid' => $user_guid,
	'joins' => ['JOIN ' . elgg_get_config('dbprefix') . 'groups_entity ge ON e.guid = ge.guid'],
	'order_by' => 'ge.name ASC',
]);
foreach ($groups as $group) {
	if ($group->thewire_enable !== 'no') {
		$options_values[$group->guid] = $group->name;
	}
}

if (count($options_values) < 2) {
	return;
}

echo elgg_view('input/select', [
	'name' => 'container_guid',
	'class' => ['mls'],
	'options_values' => $options_values,
]);
