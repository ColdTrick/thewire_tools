<?php
/**
 * Al helper functions for this plugin are bundled here
 */

/**
 * Get the max number of characters allowed in a wire post
 *
 * @return int the number of characters
 */
function thewire_tools_get_wire_length(): int {
	static $result;
	
	if (isset($result)) {
		return $result;
	}
		
	$result = (int) elgg_get_plugin_setting('limit', 'thewire');
	if ($result < 0) {
		$result = 140;
	}
	
	return $result;
}

/**
 * Save a wire post, overrules the default function because we need to support groups
 *
 * @param string $text         the text of the post
 * @param int    $userid       the owner of the post
 * @param int    $access_id    the access level of the post
 * @param int    $parent_guid  is this a reply on another post
 * @param string $method       which method was used
 * @param int    $reshare_guid is the a (re)share of some content item
 *
 * @return bool|int the GUID of the new wire post or false
 */
function thewire_tools_save_post(string $text, int $userid, int $access_id = null, int $parent_guid = 0, string $method = 'site', int $reshare_guid = 0, int $container_guid = null) {
	
	// set correct container
	if (is_null($container_guid)) {
		$container_guid = $userid;
	}
	
	if ((elgg_get_plugin_setting('enable_group', 'thewire_tools') === 'yes')) {
		// need to default to group ACL
		$group = get_entity($container_guid);
		if ($group instanceof ElggGroup) {
			$acl = $group->getOwnedAccessCollection('group_acl');
			if ($acl instanceof ElggAccessCollection) {
				if (is_null($access_id) || $group->getContentAccessMode() === \ElggGroup::CONTENT_ACCESS_MODE_MEMBERS_ONLY) {
					$access_id = $acl->id;
				}
			}
		}
	}
	
	// check the access id
	if ($access_id === ACCESS_PRIVATE) {
		// private wire posts aren't allowed
		$access_id = ACCESS_LOGGED_IN;
	}
	
	if (is_null($access_id)) {
		$access_id = ACCESS_PUBLIC;
	}
	
	// create the new post
	$post = new ElggWire();
	$post->owner_guid = $userid;
	$post->container_guid = $container_guid;
	$post->access_id = $access_id;
	
	// only xxx characters allowed (see plugin setting of thewire, 0 is unlimited)
	$max_length = thewire_tools_get_wire_length();
	if ($max_length) {
		$text = elgg_substr($text, 0, $max_length);
	}
	
	// no html tags allowed so we escape
	$post->description = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
	
	$post->method = $method; //method: site, email, api, ...
	
	$tags = thewire_get_hashtags($text);
	if (!empty($tags)) {
		$post->tags = $tags;
	}
	
	// must do this before saving so notifications pick up that this is a reply
	if ($parent_guid) {
		$post->reply = true;
	}
	
	if (!$post->save()) {
		return false;
	}
	
	// set thread guid
	if ($parent_guid) {
		$post->addRelationship($parent_guid, 'parent');
	
		// name conversation threads by guid of first post (works even if first post deleted)
		$parent_post = get_entity($parent_guid);
		$post->wire_thread = $parent_post->wire_thread;
	} else {
		// first post in this thread
		$post->wire_thread = $post->guid;
	}
	
	// add reshare
	if ($reshare_guid) {
		$post->addRelationship($reshare_guid, 'reshare');
	}
	
	// add to river
	elgg_create_river_item([
		'view' => 'river/object/thewire/create',
		'action_type' => 'create',
		'subject_guid' => $post->getOwnerGUID(),
		'object_guid' => $post->guid,
	]);
	
	// let other plugins know we are setting a user status
	$params = [
		'entity' => $post,
		'user' => $post->getOwnerEntity(),
		'message' => $post->description,
		'url' => $post->getURL(),
		'origin' => 'thewire',
	];
	elgg_trigger_plugin_hook('status', 'user', $params);

	return $post->guid;
}

/**
 * Replace urls, hash tags, and @'s by links
 *
 * @see thewire_filter()
 *
 * @param string $text The text of a post
 *
 * @return string
 */
function thewire_tools_filter(string $text): string {
	$text = ' ' . $text;

	// email addresses
	$text = preg_replace(
			'/(^|[^\w])([\w\-\.]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})/i',
			'$1<a href="mailto:$2@$3">$2@$3</a>',
			$text);

	// links
	$text = parse_urls($text);

	// hashtags
	$text = preg_replace(
			'/(^|[^\w])#(\w*[^\s\d!-\/:-@]+\w*)/',
			'$1<a href="' . elgg_get_site_url() . 'thewire/tag/$2">#$2</a>',
			$text);

	return trim($text);
}

/**
 * Get the subscription methods of the user
 *
 * @param int $user_guid the user_guid to check (default: current user)
 *
 * @return array
 */
function thewire_tools_get_notification_settings(int $user_guid = 0): array {
	
	$user_guid = (int) $user_guid;
	if (empty($user_guid)) {
		$user_guid = elgg_get_logged_in_user_guid();
	}
	
	$user = get_user($user_guid);
	if (empty($user)) {
		return [];
	}
	
	if (elgg_is_active_plugin('notifications')) {
		$saved = elgg_get_plugin_user_setting('notification_settings_saved', $user->guid, 'thewire_tools');
		if (!empty($saved)) {
			$settings = elgg_get_plugin_user_setting('notification_settings', $user->guid, 'thewire_tools');
			
			if (!empty($settings)) {
				return string_to_tag_array($settings);
			}
			
			return [];
		}
	}
	
	// default elgg settings
	$settings = (array) $user->getNotificationSettings();
	if (empty($settings)) {
		return [];
	}
	
	$result = [];
	foreach ($settings as $method => $value) {
		if (!empty($value)) {
			$result[] = $method;
		}
	}
	
	return $result;
}
