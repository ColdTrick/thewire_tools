<?php

$entity = elgg_extract('entity', $vars);
if ($entity instanceof \ElggWire) {
	echo elgg_view_field([
		'#type' => 'hidden',
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

$count = elgg_count_entities([
	'type' => 'group',
	'relationship' => 'member',
	'relationship_guid' => $user_guid,
]);
if (!$count) {
	return;
}

$container = elgg_get_page_owner_entity();
if ($container instanceof \ElggGroup) {
	if ($container->getContentAccessMode() === \ElggGroup::CONTENT_ACCESS_MODE_MEMBERS_ONLY) {
		echo elgg_view_field([
			'#type' => 'hidden',
			'name' => 'access_id',
			'value' => $container->getOwnedAccessCollection('group_acl')?->id,
		]);
		return;
	}
}

$access_options = [];
if (!elgg_get_config('walled_garden')) {
	$access_options[ACCESS_PUBLIC] = elgg_echo('thewire_tools:add:access', [elgg_echo('access:label:public')]);
}

$access_options[ACCESS_LOGGED_IN] = elgg_echo('thewire_tools:add:access', [elgg_echo('access:label:logged_in')]);
$access_options[-100] = elgg_echo('thewire_tools:add:access:group');

$access_params = [
	'#type' => 'access',
	'name' => 'access_id',
	'options_values' => $access_options,
];

echo elgg_view_field($access_params);
