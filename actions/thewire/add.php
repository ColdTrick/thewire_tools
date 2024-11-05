<?php
/**
 * Action for adding a wire post
 *
 * ColdTrick:
 * - added: container support (groups)
 * - added: share content
 */

$body = get_input('body');

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

if (elgg_get_plugin_setting('enable_group', 'thewire_tools') !== 'yes') {
	$container_guid = 0;
} else {
	$group = get_entity($container_guid);
	if ($group instanceof \ElggGroup) {
		if (!$group->isToolEnabled('thewire')) {
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
