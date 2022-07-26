<?php

/* @var $widget \ElggWidget */
$widget = elgg_extract('entity', $vars);

// get widget settings
$filter = $widget->filter;

// show add form
if (elgg_is_logged_in() && (elgg_get_plugin_setting('extend_widgets', 'thewire_tools') !== 'no')) {
	echo elgg_view_form('thewire/add');
}

$options = [
	'type' => 'object',
	'subtype' => 'thewire',
	'limit' => (int) $widget->wire_count ?: 8,
	'pagination' => false,
	'metadata_name_value_pairs_operator' => 'OR',
	'metadata_name_value_pairs' => [],
	'no_results' => elgg_echo('thewire_tools:no_result'),
	'widget_more' => elgg_view_url(elgg_generate_url('collection:object:thewire:all'), elgg_echo('thewire:moreposts')),
];

if (!empty($filter)) {
	$filters = elgg_string_to_array((string) $filter);
	foreach ($filters as $word) {
		$options['metadata_name_value_pairs'][] = [
			'name' => 'description',
			'value' => "%{$word}%",
			'operand' => 'LIKE',
			'case_sensitive' => false,
		];
	}
}

// list content
echo elgg_list_entities($options);
