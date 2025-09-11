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

// because of duplicate logic for full/summary pass through base view
echo elgg_view('object/thewire', $vars);
