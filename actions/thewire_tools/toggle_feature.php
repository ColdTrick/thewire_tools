<?php

$guid = get_input('guid');
elgg_entity_gatekeeper($guid, 'object', 'thewire');

/* @var $entity ElggWire */
$entity = get_entity($guid);

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
