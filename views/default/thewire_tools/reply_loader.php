<?php

elgg_ajax_gatekeeper();

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggWire) {
	return;
}

echo elgg_view_form('thewire/add', [
	'id' => "thewire-tools-reply-{$entity->guid}",
], [
	'post' => $entity,
]);
