<?php

/** @var \ElggWidget $widget */
$widget = elgg_extract('entity', $vars);

/** @var \ElggGroup $group */
$group = $widget->getOwnerEntity();
if ($group->isMember()) {
	echo elgg_view_form('thewire/add');
}

$options = [
	'type' => 'object',
	'subtype' => 'thewire',
	'limit' => (int) $widget->wire_count ?: 5,
	'container_guid' => $group->guid,
	'pagination' => false,
	'metadata_name_value_pairs_operator' => 'OR',
	'metadata_name_value_pairs' => [],
	'no_results' => true,
	'widget_more' => elgg_view_url($widget->getURL(), elgg_echo('thewire:moreposts')),
];

$filter = $widget->filter;
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

echo elgg_list_entities($options);
