<?php

/* @var $widget \ElggWidget */
$widget = elgg_extract('entity', $vars);

$options = [
	'type' => 'object',
	'subtype' => 'thewire',
	'limit' => (int) $widget->num_display ?: 4,
	'full_view' => false,
	'pagination' => false,
	'no_results' => elgg_echo('thewire_tools:no_result'),
	'widget_more' => elgg_view_url($widget->getURL(), elgg_echo('thewire:moreposts'))
];

$owner_entity = $widget->getOwnerEntity();
$more_url = '';
switch ($widget->owner) {
	case 'friends':
		// get users friends
		$options['relationship'] = 'friend';
		$options['relationship_guid'] = $owner_entity->guid;
		$options['relationship_join_on'] = 'container_guid';
		
		$more_url = elgg_generate_url('collection:object:thewire:friends', [
			'username' => $owner_entity->username,
		]);
		break;
	case 'all':
		// show all posts
		$more_url = elgg_generate_url('collection:object:thewire:all');
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
