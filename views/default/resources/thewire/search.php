<?php
/**
 * Search in TheWire
 */

$query = get_input('q');

elgg_push_collection_breadcrumbs('object', 'thewire');

if (!empty($query)) {
	$result = elgg_list_entities([
		'type' => 'object',
		'subtype' => 'thewire',
		'pagination' => true,
		'no_results' => true,
		'query' => $query,
		'fields' => ['metadata' => ['description']],
	], 'elgg_search');
		
	// set title
	$title_text = elgg_echo('thewire_tools:search:title', [$query]);
} else {
	$title_text = elgg_echo('thewire_tools:search:title:no_query');
	$result = elgg_echo('thewire_tools:search:no_query');
}

//build search form
$form_vars = [
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
