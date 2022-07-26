<?php

/* @var $widget ElggWidget */
$widget = elgg_extract('entity', $vars);

$num_display = (int) $widget->num_display;
$owner = $widget->owner;
$filter = $widget->filter;

if ($num_display < 1) {
	$num_display = 4;
}

$options = [
	'type' => 'object',
	'subtype' => 'thewire',
	'limit' => $num_display,
	'full_view' => false,
	'pagination' => false,
	'no_results' => elgg_echo('thewire_tools:no_result'),
];

$owner_entity = $widget->getOwnerEntity();
$more_url = '';
switch ($owner) {
	case 'friends':
		// get users friends
		$options['relationship'] = 'friend';
		$options['relationship_guid'] = $widget->owner_guid;
		$options['relationship_join_on'] = 'container_guid';
		
		$more_url = elgg_generate_url('collection:object:thewire:friends', [
			'username' => $owner_entity->username
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
			$options['container_guid'] = $widget->owner_guid;
		}
		
		break;
}

if (!empty($filter)) {
	$filters = elgg_string_to_array((string) $filter);
	
	$options['metadata_name_value_pairs'][] = [
		'name' => 'description',
		'value' => '%' . implode('%\', \'%', $filters) . '%',
		'operand' => 'like',
		'case_sensitive' => false,
	];
}

if ($owner_entity instanceof \ElggGroup) {
	$more_url = elgg_generate_url('collection:object:thewire:group', [
		'username' => $owner_entity->guid
	]);
} elseif (empty($more_url) && $owner_entity instanceof \ElggUser) {
	$more_url = elgg_generate_url('collection:object:thewire:owner', [
		'username' => $owner_entity->username
	]);
}

if (!empty($more_url)) {
	$options['widget_more'] = elgg_view_url($more_url, elgg_echo('thewire:moreposts'));
}

// show add form in widget
if (elgg_is_logged_in() && (elgg_get_plugin_setting('extend_widgets', 'thewire_tools') != 'no')) {
	echo elgg_view_form('thewire/add');
}

// list content
echo elgg_list_entities($options);
