<?php

/* @var $widget ElggWidget */
$widget = $vars['entity'];

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
];

$more_url = '';
switch ($owner) {
	case 'friends':
		// get users friends
		$options['relationship'] = 'friend';
		$options['relationship_guid'] = $widget->owner_guid;
		$options['relationship_join_on'] = 'container_guid';
		
		$more_url = elgg_generate_url('collection:object:thewire:friends', [
			'username' => $widget->getOwnerEntity()->username
		]);
		break;
	case 'all':
		// show all posts
		$more_url = elgg_generate_url('collection:object:thewire:all');
		break;
	default:
		$options['container_guid'] = $widget->getOwnerGUID();
		$more_url = elgg_generate_url('collection:object:thewire:owner', [
			'username' => $widget->getOwnerEntity()->username
		]);
		break;
}


if (!empty($filter)) {
	$filters = string_to_tag_array($filter);
	$filters = array_map('sanitise_string', $filters);
	
	$options['metadata_name_value_pairs'][] = [
		'name' => 'description',
		'value' => '%' . implode('%\', \'%', $filters) . '%',
		'operand' => 'like',
		'case_sensitive' => false,
	];
}

// show add form in widget
if (elgg_is_logged_in() && (elgg_get_plugin_setting('extend_widgets', 'thewire_tools') != 'no')) {
	echo elgg_view_form('thewire/add');
}

// list content
$content = elgg_list_entities($options);
if (empty($error) && !empty($content)) {
	echo $content;
	
	echo elgg_format_element('div', ['class' => 'elgg-widget-more'], elgg_view('output/url', [
		'href' => $more_url,
		'text' => elgg_echo('thewire:moreposts'),
		'is_trusted' => true,
	]));
} else {
	echo elgg_echo('thewire_tools:no_result');
}
