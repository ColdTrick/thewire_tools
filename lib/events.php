<?php
/**
 * All event handler functions are bundled in this file
 */

/**
 * This functions performs actions when a wire post is created
 *
 * @param string     $event  'create'
 * @param string     $type   'object'
 * @param ElggObject $object the ElggObject created
 *
 * @return void
 */
function thewire_tools_create_object_event_handler($event, $type, ElggObject $object) {
	
	if (empty($object) || !elgg_instanceof($object, "object", "thewire")) {
		return;
	}
	
	//send out notification to users mentioned in a wire post
	$usernames = array();
	preg_match_all("/\@([A-Za-z0-9\_\.\-]+)/i", $object->description, $usernames);
	
	if (empty($usernames)) {
		return;
	}
	
	$usernames = array_unique($usernames[0]);
	$params = array(
		"object" => $object,
		"action" => "mention"
	);
	
	foreach ($usernames as $username) {
		$username = str_ireplace("@", "", $username);
		$user = get_user_by_username($username);
		
		if (empty($user) || ($user->getGUID() == $object->getOwnerGUID())) {
			continue;
		}
			
		$setting = thewire_tools_get_notification_settings($user->getGUID());
		if (empty($setting)) {
			continue;
		}
		
		$subject = elgg_echo("thewire_tools:notify:mention:subject");
		$message = elgg_echo("thewire_tools:notify:mention:message", array(
			$user->name,
			$object->getOwnerEntity()->name,
			elgg_normalize_url("thewire/search/@" . $user->username)
		));
		
		notify_user($user->getGUID(), $object->getOwnerGUID(), $subject, $message, $params, $setting);
	}
}
