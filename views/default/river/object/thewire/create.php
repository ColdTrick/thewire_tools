<?php

/* @var $item ElggRiverItem */
$item = elgg_extract('item', $vars);

$object = $item->getObjectEntity();
$excerpt = elgg_get_excerpt($object->description);
$excerpt = thewire_tools_filter($excerpt);
if (substr($excerpt, -3) === '...') {
	// add read more link
	$excerpt .= '&nbsp;' . elgg_view('output/url', [
		'text' => strtolower(elgg_echo('more')),
		'href' => $object->getURL(),
		'is_trusted' => true,
	]);
}

$subject = $item->getSubjectEntity();
$subject_link = elgg_view('output/url', [
	'href' => $subject->getURL(),
	'text' => $subject->getDisplayName(),
	'class' => 'elgg-river-subject',
	'is_trusted' => true,
]);

$object_link = elgg_view('output/url', [
	'href' => elgg_generate_url('collection:object:thewire:thread', [
		'guid' => $object->wire_thread,
	]) . "#elgg-object-{$object->guid}",
	'text' => elgg_echo('thewire:wire'),
	'class' => 'elgg-river-object',
	'is_trusted' => true,
]);

$summary = elgg_echo('river:object:thewire:create', [$subject_link, $object_link]);

$container = $object->getContainerEntity();
if ($container instanceof ElggGroup && $container->guid != elgg_get_page_owner_guid()) {
	$group_link = elgg_view('output/url', [
		'href' => $container->getURL(),
		'text' => $container->getDisplayName(),
		'is_trusted' => true,
	]);
	$summary .= ' ' .  elgg_echo('river:ingroup', [$group_link]);
}

$attachments = '';
$reshare = elgg_call(ELGG_IGNORE_ACCESS, function() use ($object) {
	return $object->getEntitiesFromRelationship([
		'relationship' => 'reshare',
		'limit' => 1,
	]);
});

if (!empty($reshare)) {
	$attachments = elgg_view('thewire_tools/reshare_source', ['entity' => $reshare[0]]);
}

echo elgg_view('river/elements/layout', [
	'item' => $item,
	'message' => elgg_view('output/longtext', ['value' => $excerpt]),
	'summary' => $summary,
	'attachments' => $attachments,
]);
