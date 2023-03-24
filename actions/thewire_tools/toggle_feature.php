<?php

$guid = (int) get_input('guid');
$entity = get_entity($guid);
if (!$entity instanceof \ElggWire) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$container = $entity->getContainerEntity();
if ($container instanceof \ElggGroup) {
	if (!$container->canEdit()) {
		return elgg_error_response(elgg_echo('actionunauthorized'));
	}
} elseif (!elgg_is_admin_logged_in()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

if (!empty($entity->featured)) {
	unset($entity->featured);
	
	$message = elgg_echo('thewire_tools:action:toggle_feature:unfeatured');
} else {
	$entity->featured = time();
	
	$message = elgg_echo('thewire_tools:action:toggle_feature:featured');
}

return elgg_ok_response('', $message);
