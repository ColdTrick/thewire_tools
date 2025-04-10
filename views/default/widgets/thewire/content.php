<?php
/**
 * User wire post widget display view
 */

$widget = elgg_extract('entity', $vars);
if (!$widget instanceof \ElggWidget) {
	return;
}

$num_display = (int) $widget->num_display ?: 4;

$options = [
	'type' => 'object',
	'subtype' => 'thewire',
	'limit' => $num_display,
	'full_view' => false,
	'pagination' => false,
	'no_results' => elgg_echo('thewire_tools:no_result'),
	'widget_more' => elgg_view_url($widget->getURL(), elgg_echo('thewire:moreposts'))
];

$owner_entity = $widget->getOwnerEntity();
switch ($widget->owner) {
	case 'friends':
		// get users friends
		$options['relationship'] = 'friend';
		$options['relationship_guid'] = $owner_entity->guid;
		$options['relationship_join_on'] = 'container_guid';
		
		break;
	case 'all':
		// show all posts
		break;
	default:
		if ($owner_entity instanceof \ElggUser) {
			$options['owner_guid'] = $owner_entity->guid;
		} else {
			$options['container_guid'] = $owner_entity->guid;
		}
		break;
}

$filter = $widget->filter;
if (!empty($filter)) {
	$filters = elgg_string_to_array((string) $filter);
	$options['metadata_name_value_pairs_operator'] = 'OR';
	foreach ($filters as $word) {
		$options['metadata_name_value_pairs'][] = [
			'name' => 'description',
			'value' => "%{$word}%",
			'operand' => 'LIKE',
			'case_sensitive' => false,
		];
	}
}

// show add form in widget
if (elgg_is_logged_in() && elgg_get_plugin_setting('extend_widgets', 'thewire_tools') !== 'no') {
	echo elgg_view_form('thewire/add');
}

// list content
echo elgg_list_entities($options);
