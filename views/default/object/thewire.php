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

$full = (bool) elgg_extract('full_view', $vars, false);

$route = _elgg_services()->request->getRoute();
if (!empty($route) && $route->getName() === 'collection:object:thewire:thread') {
// 	$full = true;
}
 
// make compatible with posts created with original Curverider plugin
$thread_id = $entity->wire_thread;
if (!$thread_id) {
	$entity->wire_thread = $entity->guid;
}

$show_thread = false;
if (!elgg_in_context('thewire_tools_thread') && !$full) {
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
			'href' => false,
			'is_trusted' => true,
			'class' => ['mls', 'elgg-toggle'],
			'data-toggle-selector' => "#thewire-full-view-{$entity->guid}, #thewire-summary-view-{$entity->guid}",
		]);
		$more_content = $entity->description;
	} else {
		$text = $entity->description;
	}
}

elgg_push_context('input');
$content = elgg_view('output/longtext', [
	'value' => elgg_sanitize_input(thewire_tools_filter($text)) . $more_link,
	'id' => "thewire-summary-view-{$entity->guid}",
	'data-toggle-slide' => 0,
	'sanitize' => false, // already done and will cause issues with the more link
]);

if (!empty($more_content)) {
	$content .= elgg_view('output/longtext', [
		'value' => elgg_sanitize_input(thewire_tools_filter($more_content)),
		'id' => "thewire-full-view-{$entity->guid}",
		'class' => 'hidden',
		'sanitize' => false, // already done
	]);
}
elgg_pop_context();

// check for reshare entity (ignore access while doing so as shared entity could be unaccessable)
$reshare = elgg_call(ELGG_IGNORE_ACCESS, function() use ($entity) {
	return $entity->getEntitiesFromRelationship(['relationship' => 'reshare', 'limit' => 1]);
});

if (!empty($reshare)) {
	$content .= elgg_format_element('div', [
		'class' => [
			'thewire-tools-reshare-source-wrapper',
			'elgg-divide-left',
			'pls',
		],
	], elgg_view('thewire_tools/reshare_source', [
		'entity' => $reshare[0],
	]));
}

if (elgg_is_logged_in() && !elgg_in_context('thewire_tools_thread')) {
	$form_vars = [
		'id' => "thewire-tools-reply-{$entity->guid}",
		'class' => 'hidden',
	];
	$content .= elgg_view_form('thewire/add', $form_vars, ['post' => $entity]);
}

$params = [
	'title' => false,
	'access' => false,
	'tags' => false,
	'icon_entity' => $entity->getOwnerEntity(),
];

if ($full) {
	$params['body'] = $content;
	$params['show_summary'] = true;
	
	$params = $params + $vars;
	echo elgg_view('object/elements/full', $params);
} else {
	$params['content'] = $content;
	
	$params = $params + $vars;
	echo elgg_view('object/elements/summary', $params);
}

if ($show_thread) {
	echo elgg_format_element('div', [
		'id' => "thewire-thread-{$entity->guid}",
		'class' => 'thewire-thread',
		'data-thread' => $entity->wire_thread,
		'data-guid' => $entity->guid,
	]);
}
