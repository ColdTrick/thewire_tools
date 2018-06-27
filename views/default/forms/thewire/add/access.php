<?php

$entity = elgg_extract('entity', $vars);
if ($entity) {
	echo elgg_view('input/hidden', [
		'name' => 'access_id',
		'value' => $entity->access_id,
	]);
	return;
}

if (elgg_get_plugin_setting('enable_group', 'thewire_tools') !== 'yes') {
	return;
}

$user_guid = elgg_get_logged_in_user_guid();
if (!$user_guid) {
	return;
}

$count = elgg_get_entities([
	'type' => 'group',
	'count' => true,
	'relationship' => 'member',
	'relationship_guid' => $user_guid,
]);
if (!$count) {
	return;
}

$access_options = [];
if (!elgg_get_config('walled_garden')) {
	$access_options[ACCESS_PUBLIC] = elgg_echo('thewire_tools:add:access', [elgg_echo('PUBLIC')]);
}

$access_options[ACCESS_LOGGED_IN] = elgg_echo('thewire_tools:add:access', [elgg_echo('LOGGED_IN')]);
$access_options[-100] = elgg_echo('thewire_tools:add:access:group');

$access_params = [
	'name' => 'access_id',
	'class' => ['mls'],
	'options_values' => $access_options,
];

if (elgg_in_context('widgets')) {
	$access_params['class'][] = 'thewire-tools-widget-access';
}

echo elgg_view('input/access', $access_params);
