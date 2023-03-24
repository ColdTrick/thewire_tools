<?php

/* @var $plugin \ElggPlugin */
$plugin = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('thewire_tools:settings:enable_group'),
	'name' => 'params[enable_group]',
	'default' => 'no',
	'value' => 'yes',
	'checked' => $plugin->enable_group === 'yes',
	'switch' => true,
]);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('thewire_tools:settings:extend_widgets'),
	'name' => 'params[extend_widgets]',
	'default' => 'no',
	'value' => 'yes',
	'checked' => $plugin->extend_widgets === 'yes',
	'switch' => true,
]);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('thewire_tools:settings:extend_activity'),
	'name' => 'params[extend_activity]',
	'default' => 'no',
	'value' => 'yes',
	'checked' => $plugin->extend_activity === 'yes',
	'switch' => true,
]);
