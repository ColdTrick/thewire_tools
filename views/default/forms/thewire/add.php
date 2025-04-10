<?php
/**
 * Wire add form body
 *
 * @uses $vars['post']
 */

elgg_require_css('forms/thewire/add');

$post = elgg_extract('post', $vars);
$char_limit = (int) elgg_get_plugin_setting('limit', 'thewire');
$reshare = elgg_extract('reshare', $vars); // for reshare functionality

$text = elgg_echo('post');
if ($post instanceof \ElggWire) {
	$text = elgg_echo('reply');
	
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'parent_guid',
		'value' => $post->guid,
	]);
}

if ($char_limit && !elgg_is_active_plugin('ckeditor')) {
	elgg_import_esm('forms/thewire/add');
}

$post_value = '';
if ($reshare instanceof \ElggEntity) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'reshare_guid',
		'value' => $reshare->guid,
	]);
	
	echo elgg_view('thewire_tools/reshare_source', [
		'entity' => $reshare,
	]);
	
	$post_value = $reshare->getDisplayName();
	if (empty($post_value)) {
		$post_value = (string) $reshare->description;
	}
	
	$post_value = elgg_get_excerpt($post_value, 140);
	$post_value = htmlspecialchars_decode($post_value, ENT_QUOTES);
}

$fields = elgg()->fields->get('object', 'thewire');

foreach ($fields as $field) {
	$name = $field['name'];
	$default = null;
	
	if ($name === 'description') {
		$default = $post_value;
	}
	
	$field['value'] = elgg_extract($name, $vars, $default);
	
	echo elgg_view_field($field);
}

// form footer
$fields = [
	[
		'#type' => 'submit',
		'text' => $text,
	],
	[
		'#html' => elgg_view('forms/thewire/add/container', [
			'entity' => $post,
		]),
	],
	[
		'#html' => elgg_view('forms/thewire/add/access', [
			'entity' => $post,
		]),
	],
];

if ($char_limit > 0) {
	$count_down = elgg_format_element('span', [], $char_limit) . ' ' . elgg_echo('thewire:charleft');
	
	$chars = elgg_format_element('div', ['class' => 'elgg-field-input'], $count_down);
	
	$fields[] = [
		'#html' => elgg_format_element('div', ['class' => ['elgg-field', 'thewire-characters-wrapper']], $chars),
	];
}

$footer = elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'class' => 'elgg-fieldset-wrap',
	'fields' => $fields,
]);

elgg_set_form_footer($footer);
