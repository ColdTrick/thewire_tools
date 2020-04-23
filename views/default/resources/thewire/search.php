<?php
/**
 * Search in TheWire
 */

$query = get_input('q');

elgg_push_collection_breadcrumbs('object', 'thewire');

if (!empty($query)) {
	$options = [
		'type' => 'object',
		'subtype' => 'thewire',
		'pagination' => true,
		'no_results' => true,
		'metadata_name_value_pairs_operator' => 'OR',
		'metadata_name_value_pairs' => [],
	];
	
	$where_options = explode(' ', $query);
	if (!empty($where_options)) {
		foreach ($where_options as $word) {
			$options['metadata_name_value_pairs'][] = [
				'name' => 'description',
				'value' => "%{$word}%",
				'operand' => 'LIKE',
				'case_sensitive' => false,
			];
		}
	}
	
	$result = elgg_list_entities($options);
		
	// set title
	$title_text = elgg_echo('thewire_tools:search:title', [$query]);
} else {
	$title_text = elgg_echo('thewire_tools:search:title:no_query');
	$result = elgg_echo('thewire_tools:search:no_query');
}

//build search form
$form_vars = [
	'id' => 'thewire_tools_search_form',
	'action' => 'thewire/search',
	'disable_security' => true,
	'method' => 'GET',
];
$body_vars = ['query' => $query];

$form = elgg_view_form('thewire/search', $form_vars , $body_vars);

// Display page
echo elgg_view_page($title_text, [
	'content' => $form . $result,
]);
