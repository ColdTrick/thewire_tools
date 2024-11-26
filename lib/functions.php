<?php
/**
 * All helper functions for this plugin are bundled here
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
 * @param string $text           the text of the post
 * @param int    $userid         the owner of the post
 * @param int    $access_id      the access level of the post
 * @param int    $parent_guid    is this a reply on another post
 * @param string $method         which method was used
 * @param int    $reshare_guid   is the a (re)share of some content item
 * @param int    $container_guid container of the wire post
 *
 * @return bool|int the GUID of the new wire post or false
 */
function thewire_tools_save_post(string $text, int $userid, int $access_id = null, int $parent_guid = 0, string $method = 'site', int $reshare_guid = 0, int $container_guid = 0) {
	// set correct container
	if ($container_guid < 1) {
		$container_guid = $userid;
	}
	
	if (elgg_get_plugin_setting('enable_group', 'thewire_tools') === 'yes') {
		// need to default to group ACL
		$group = get_entity($container_guid);
		if ($group instanceof \ElggGroup) {
			$acl = $group->getOwnedAccessCollection('group_acl');
			if ($acl instanceof \ElggAccessCollection) {
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
	$post = new \ElggWire();
	$post->owner_guid = $userid;
	$post->container_guid = $container_guid;
	$post->access_id = $access_id;
	
	$text = $text ?? '';
	$text = trim(str_replace('&nbsp;', ' ', $text));
	
	// Character limit is now from config
	$limit = thewire_tools_get_wire_length();
	if ($limit > 0) {
		$text_for_size = elgg_strip_tags($text);
		if (elgg_strlen($text_for_size) > $limit) {
			return false;
		}
	}
	
	// no html tags allowed so we strip (except links (a) for mention support)
	$text = elgg_strip_tags($text, '<a>');
	
	// no html tags allowed so we escape
	$post->description = $text;
	
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
		'subject_guid' => $post->owner_guid,
		'object_guid' => $post->guid,
	]);
	
	return $post->guid;
}
