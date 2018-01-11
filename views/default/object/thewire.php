<?php
/**
 * View a wire post
 *
 * @uses $vars["entity"]
 */

elgg_load_js('elgg.thewire');

$full = (bool) elgg_extract('full_view', $vars, false);
$post = elgg_extract('entity', $vars, false);

if (!$post) {
	return true;
}

if (elgg_in_context('thewire_thread')) {
	$full = true;
}

// make compatible with posts created with original Curverider plugin
$thread_id = $post->wire_thread;
if (!$thread_id) {
	$post->wire_thread = $post->guid;
}

$show_thread = false;
if (!elgg_in_context('thewire_tools_thread') && !elgg_in_context('thewire_thread')) {
	if ($post->countEntitiesFromRelationship('parent') || $post->countEntitiesFromRelationship('parent', true)) {
		$show_thread = true;
	}
}

$owner = $post->getOwnerEntity();
$owner_icon = elgg_view_entity_icon($owner, 'tiny');

$subtitle = elgg_view('page/elements/by_line', [
	'entity' => $post,
	'owner_url' => "thewire/owner/{$owner->username}",
]);

$metadata = elgg_view_menu('entity', [
	'entity' => $post,
	'handler' => 'thewire',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
]);

// show text different in widgets
$text = $post->description;
$more_link = '';
$more_content = '';
if (elgg_in_context('widgets')) {
	$text = elgg_get_excerpt($text, 140);
	
	// show more link?
	if (substr($text, -3) == '...') {
		$more_link = elgg_view('output/url', [
			'text' => elgg_echo('more'),
			'href' => $post->getURL(),
			'is_trusted' => true,
			'class' => 'mls',
		]);
	}
} elseif (!$full) {
	$text = elgg_get_excerpt($text);
	
	// show more link?
	if (substr($text, -3) == '...') {
		$more_link = elgg_view('output/url', [
			'text' => elgg_echo('more'),
			'href' => "#thewire-full-view-{$post->getGUID()},#thewire-summary-view-{$post->getGUID()}",
			'is_trusted' => true,
			'rel' => 'toggle',
			'class' => 'mls',
			'data-toggle-selector' => "#thewire-full-view-{$post->getGUID()}, #thewire-summary-view-{$post->getGUID()}",
		]);
		$more_content = $post->description;
	} else {
		$text = $post->description;
	}
}

elgg_push_context('input');
$content = elgg_view('output/longtext', [
	'value' => thewire_tools_filter($text) . $more_link,
	'id' => "thewire-summary-view-{$post->getGUID()}",
	'data-toggle-slide' => 0,
]);

if (!empty($more_content)) {
	$content .= elgg_view('output/longtext', [
		'value' => thewire_tools_filter($more_content),
		'id' => "thewire-full-view-{$post->getGUID()}",
		'class' => 'hidden',
	]);
}
elgg_pop_context();

// check for reshare entity (ignore access while doing so as shared entity could be unaccessable)
$ia = elgg_set_ignore_access(true);
$reshare = $post->getEntitiesFromRelationship(['relationship' => 'reshare', 'limit' => 1]);
elgg_set_ignore_access($ia);

if (!empty($reshare)) {
	$content .= elgg_format_element('div', [
		'class' => 'elgg-divide-left pls',
	], elgg_view('thewire_tools/reshare_source', [
		'entity' => $reshare[0],
	]));
}

if (elgg_is_logged_in() && !elgg_in_context('thewire_tools_thread')) {
	$form_vars = [
		'id' => 'thewire-tools-reply-' . $post->getGUID(),
		'class' => 'hidden',
	];
	$content .= elgg_view_form('thewire/add', $form_vars, ['post' => $post]);
}

$params = [
	'entity' => $post,
	'metadata' => $metadata,
	'subtitle' => $subtitle,
	'content' => $content,
	'tags' => false,
];
$params = $params + $vars;
$list_body = elgg_view('object/elements/summary', $params);

echo elgg_view_image_block($owner_icon, $list_body);

if ($show_thread) {
	echo elgg_format_element('div', [
		'id' => "thewire-thread-{$post->getGUID()}",
		'class' => 'thewire-thread',
		'data-thread' => $post->wire_thread,
		'data-guid' => $post->getGUID(),
	]);
}
