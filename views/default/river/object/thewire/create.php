<?php
$item = elgg_extract('item', $vars);

$object = $item->getObjectEntity();
$excerpt = strip_tags($object->description);
$excerpt = thewire_tools_filter($excerpt);

$subject = $item->getSubjectEntity();
$subject_link = elgg_view('output/url', [
	'href' => $subject->getURL(),
	'text' => $subject->name,
	'class' => 'elgg-river-subject',
	'is_trusted' => true,
]);

$object_link = elgg_view('output/url', [
	'href' => "thewire/owner/$subject->username",
	'text' => elgg_echo('thewire:wire'),
	'class' => 'elgg-river-object',
	'is_trusted' => true,
]);

$summary = elgg_echo('river:create:object:thewire', [$subject_link, $object_link]);

$attachments = '';
$reshare = $object->getEntitiesFromRelationship(['relationship' => 'reshare', 'limit' => 1]);
if (!empty($reshare)) {
	$attachments = elgg_view('thewire_tools/reshare_source', ['entity' => $reshare[0]]);
}

echo elgg_view('river/elements/layout', [
	'item' => $item,
	'message' => $excerpt,
	'summary' => $summary,
	'attachments' => $attachments,
]);