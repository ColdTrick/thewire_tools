<?php

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
];

if (!empty($filter)) {
	$filters = string_to_tag_array($filter);
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
$content = elgg_list_entities($options);
if (empty($content)) {
	echo elgg_echo('thewire_tools:no_result');
	return;
}
	
echo $content;

$more_link = elgg_view('output/url', [
	'href' => 'thewire/all',
	'text' => elgg_echo('thewire:moreposts'),
	'is_trusted' => true,
]);
echo elgg_format_element('div', ['class' => 'elgg-widget-more'], $more_link);
