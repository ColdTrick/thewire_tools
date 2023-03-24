<?php
/**
 * Show all Wire posts in a group
 *
 * @uses $vars['entity'] the group
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggGroup) {
	return;
}

$vars['options'] = [
	'container_guid' => $entity->guid,
	'preload_containers' => false,
];

echo elgg_view('thewire/listing/all', $vars);
