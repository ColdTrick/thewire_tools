<?php
/**
 * Group blog module
 */

$params = [
	'entity_type' => 'object',
	'entity_subtype' => 'thewire',
	'add_link' => false,
];
$params = $params + $vars;
echo elgg_view('groups/profile/module', $params);
