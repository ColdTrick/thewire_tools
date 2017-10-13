<?php
/**
 * Action for adding a wire post
 */

// don't filter since we strip and filter escapes some characters
$body = get_input('body', '', false);

$access_id = (int) get_input('access_id', ACCESS_PUBLIC);
$container_guid = (int) get_input('container_guid');

if ($access_id == -100) {
	$access_id = null;
}

$parent_guid = (int) get_input('parent_guid');
$reshare_guid = (int) get_input('reshare_guid');

// make sure the post isn't blank
if (empty($body)) {
	return elgg_error_response(elgg_echo('thewire:blank'));
}

if (!thewire_tools_groups_enabled()) {
	$container_guid = null;
} else {
	$group = get_entity($container_guid);
	if ($group instanceof ElggGroup) {
		if ($group->thewire_enable == 'no') {
			// not allowed to post in this group
			return elgg_error_response(elgg_echo('thewire_tools:groups:error:not_enabled'));
		}
	}
}

$guid = thewire_tools_save_post($body, elgg_get_logged_in_user_guid(), $access_id, $parent_guid, 'site', $reshare_guid, $container_guid);
if (!$guid) {
	return elgg_error_response(elgg_echo('thewire:notsaved'));
}

return elgg_ok_response('', elgg_echo('thewire:posted'));
