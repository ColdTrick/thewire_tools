<?php
/**
 * Wire add form body
 *
 * @uses $vars['post']
 */

elgg_require_js('elgg/thewire');

if (!elgg_is_active_plugin('mentions')) {
	// mentions not enabled, use our version of autocomplete
	elgg_require_js('thewire_tools/autocomplete');
}

$post = elgg_extract('post', $vars);
$char_limit = thewire_tools_get_wire_length();
$reshare = elgg_extract('reshare', $vars); // for reshare functionality

$text = elgg_echo('post');
if ($post) {
	$text = elgg_echo('reply');
}
$chars_left = elgg_echo('thewire:charleft');

$parent_input = '';
$mentions = '';
$container_input = elgg_view('forms/thewire/add/container', ['entity' => $post]);
$access_input = elgg_view('forms/thewire/add/access', ['entity' => $post]);
$reshare_input = '';
$post_value = '';

if ($post) {
	$parent_input = elgg_view('input/hidden', [
		'name' => 'parent_guid',
		'value' => $post->guid,
	]);
}

if (!empty($reshare)) {
	$reshare_input = elgg_view('input/hidden', [
		'name' => 'reshare_guid',
		'value' => $reshare->getGUID(),
	]);
	
	$reshare_input .= elgg_view('thewire_tools/reshare_source', ['entity' => $reshare]);
	
	if (!empty($reshare->title)) {
		$post_value = $reshare->title;
	} elseif (!empty($reshare->name)) {
		$post_value = $reshare->name;
	} elseif (!empty($reshare->description)) {
		$post_value = elgg_get_excerpt($reshare->description, 140);
	}
	
	$post_value = htmlspecialchars_decode($post_value, ENT_QUOTES);
}

$count_down = "<span>$char_limit</span> $chars_left";
$num_lines = 2;
if ($char_limit == 0) {
	$num_lines = 3;
	$count_down = '';
} else if ($char_limit > 140) {
	$num_lines = 3;
}

$post_input = elgg_view('input/plaintext', [
	'name' => 'body',
	'class' => 'mtm',
	'id' => 'thewire-textarea',
	'rows' => $num_lines,
	'data-max-length' => $char_limit,
	'required' => true,
	'value' => $post_value,
	'placeholder' => elgg_echo('thewire:form:body:placeholder'),
]);

$submit_button = elgg_view_field([
	'#type' => 'submit',
	'value' => $text,
	'id' => 'thewire-submit-button',
]);

echo $reshare_input;
echo $post_input;
echo elgg_format_element('div', ['id' => 'thewire-characters-remaining'], $count_down);

$footer = elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'fields' => [
		[
			'#html' => $parent_input . $submit_button . $container_input . $access_input,
		]
	],
]);

elgg_set_form_footer($footer);
