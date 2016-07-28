<?php
/**
 * Action for adding a wire post
 *
 */

// don't filter since we strip and filter escapes some characters
$body = get_input('body', '', false);

$access_id = (int) get_input('access_id', ACCESS_PUBLIC);
$method = 'site';
$parent_guid = (int) get_input('parent_guid');
$reshare_guid = (int) get_input('reshare_guid');

// make sure the post isn't blank
if (empty($body)) {
	register_error(elgg_echo('thewire:blank'));
	forward(REFERER);
}

$guid = thewire_tools_save_post($body, elgg_get_logged_in_user_guid(), $access_id, $parent_guid, $method, $reshare_guid);
if (!$guid) {
	register_error(elgg_echo('thewire:notsaved'));
	forward(REFERER);
}

system_message(elgg_echo('thewire:posted'));
forward(REFERER);
