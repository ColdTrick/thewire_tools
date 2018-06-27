<?php

$widget = elgg_extract('entity', $vars);

echo elgg_view('object/widget/edit/num_display', [
	'entity' => $widget,
	'name' => 'wire_count',
	'label' => elgg_echo('thewire:num'),
	'min' => 1,
	'max' => 10,
	'default' => 5,
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('widgets:thewire:filter'),
	'name' => 'params[filter]',
	'value' => $widget->filter,
]);
