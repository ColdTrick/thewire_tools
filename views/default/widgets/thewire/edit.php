<?php

/* @var $widget \ElggWidget */
$widget = elgg_extract('entity', $vars);

echo elgg_view('object/widget/edit/num_display', [
	'entity' => $widget,
	'label' => elgg_echo('thewire:num'),
	'default' => 4,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('widgets:thewire:owner'),
	'name' => 'params[owner]',
	'options_values' => [
		'mine' => elgg_echo('mine'),
		'friends' => elgg_echo('friends'),
		'all' => elgg_echo('all'),
	],
	'value' => $widget->owner,
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('widgets:thewire:filter'),
	'name' => 'params[filter]',
	'value' => $widget->filter,
]);
