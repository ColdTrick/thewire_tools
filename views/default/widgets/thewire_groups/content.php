<?php

$widget = elgg_extract('entity', $vars);
$group = $widget->getOwnerEntity();

$filter = $widget->filter;

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

$list = elgg_list_entities($options);
if (empty($list)) {
	echo elgg_echo('thewire_tools:no_result');
	return;
}

echo $list;

$more_link = elgg_view('output/url', [
	'href' => "thewire/group/{$widget->container_guid}",
	'text' => elgg_echo('thewire:moreposts'),
	'is_trusted' => true,
]);
echo elgg_format_element('div', ['class' => 'elgg-widget-more'], $more_link);
