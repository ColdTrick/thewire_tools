<?php
/**
 * View a wire post
 *
 * @uses $vars['entity'] ElggWire to show
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggWire) {
	return;
}

elgg_require_js('elgg/thewire');

$full = (bool) elgg_extract('full_view', $vars, false);

if (elgg_in_context('thewire_thread')) {
	$full = true;
}

// make compatible with posts created with original Curverider plugin
$thread_id = $entity->wire_thread;
if (!$thread_id) {
	$entity->wire_thread = $entity->guid;
}

$show_thread = false;
if (!elgg_in_context('thewire_tools_thread') && !elgg_in_context('thewire_thread')) {
	if ($entity->countEntitiesFromRelationship('parent') || $entity->countEntitiesFromRelationship('parent', true)) {
		$show_thread = true;
	}
}

// show text different in widgets
$text = $entity->description;
$more_link = '';
$more_content = '';
if (elgg_in_context('widgets')) {
	$text = elgg_get_excerpt($text, 140);
	
	// show more link?
	if (substr($text, -3) == '...') {
		$more_link = elgg_view('output/url', [
			'text' => elgg_echo('more'),
			'href' => $entity->getURL(),
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
			'href' => "#thewire-full-view-{$entity->getGUID()},#thewire-summary-view-{$entity->getGUID()}",
			'is_trusted' => true,
			'rel' => 'toggle',
			'class' => 'mls',
			'data-toggle-selector' => "#thewire-full-view-{$entity->getGUID()}, #thewire-summary-view-{$entity->getGUID()}",
		]);
		$more_content = $entity->description;
	} else {
		$text = $entity->description;
	}
}

elgg_push_context('input');
$content = elgg_view('output/longtext', [
	'value' => thewire_tools_filter($text) . $more_link,
	'id' => "thewire-summary-view-{$entity->getGUID()}",
	'data-toggle-slide' => 0,
]);

if (!empty($more_content)) {
	$content .= elgg_view('output/longtext', [
		'value' => thewire_tools_filter($more_content),
		'id' => "thewire-full-view-{$entity->getGUID()}",
		'class' => 'hidden',
	]);
}
elgg_pop_context();

// check for reshare entity (ignore access while doing so as shared entity could be unaccessable)
$ia = elgg_set_ignore_access(true);
$reshare = $entity->getEntitiesFromRelationship(['relationship' => 'reshare', 'limit' => 1]);
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
		'id' => 'thewire-tools-reply-' . $entity->getGUID(),
		'class' => 'hidden',
	];
	$content .= elgg_view_form('thewire/add', $form_vars, ['post' => $entity]);
}

$params = [
	'title' => false,
	'access' => false,
	'content' => $content,
	'tags' => false,
	'icon_entity' => $entity->getOwnerEntity(),
];
$params = $params + $vars;
echo elgg_view('object/elements/summary', $params);

if ($show_thread) {
	echo elgg_format_element('div', [
		'id' => "thewire-thread-{$entity->getGUID()}",
		'class' => 'thewire-thread',
		'data-thread' => $entity->wire_thread,
		'data-guid' => $entity->guid,
	]);
}
