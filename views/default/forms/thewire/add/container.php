<?php
/**
 * Container selector for writing a wire post
 *
 * @uses $vars['entity']  an existing wire post
 * @uses $vars['reshare'] a shared entity
 */

$entity = elgg_extract('entity', $vars);
if ($entity instanceof \ElggWire) {
	if ($entity->getContainerEntity() instanceof \ElggGroup) {
		// only set container if it's a group
		echo elgg_view_field([
			'#type' => 'hidden',
			'name' => 'container_guid',
			'value' => $entity->container_guid,
		]);
	}
	
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

$user = elgg_get_logged_in_user_entity();
if (!$user instanceof \ElggUser) {
	return;
}

$reshare = elgg_extract('reshare', $vars);

$options_values = [
	$user->guid => elgg_echo('thewire_tools:add:container:site'),
];

$groups = $user->getGroups([
	'limit' => false,
	'batch' => true,
	'sort_by' => [
		'property' => 'name',
		'direction' => 'ASC',
	],
]);

/** @var \ElggGroup $group */
foreach ($groups as $group) {
	$option = [
		'text' => $group->getDisplayName(),
		'value' => $group->guid,
	];
	
	if (!$group->isToolEnabled('thewire')) {
		$option['disabled'] = true;
		$option['text'] .= ' ' . elgg_echo('thewire_tools:share:group:disabled');
	} elseif ($reshare instanceof \ElggEntity) {
		if ($reshare->container_guid === $group->guid) {
			$option['disabled'] = true;
			$option['text'] .= ' ' . elgg_echo('thewire_tools:share:group:group_content');
		} elseif (!(bool) $group->getPluginSetting('thewire_tools', 'enable_reshare', true)) {
			$option['disabled'] = true;
			$option['text'] .= ' ' . elgg_echo('thewire_tools:share:group:not_allowed');
		}
	}
	
	$options_values[$group->guid] = $option;
}

if (count($options_values) < 2) {
	return;
}

echo elgg_view_field([
	'#type' => 'select',
	'name' => 'container_guid',
	'options_values' => $options_values,
]);
