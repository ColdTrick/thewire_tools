<?php

	/**
	 * This functions performs actions when a wire post is created
	 *
	 * @param string $event
	 * @param string $type
	 * @param ElggObject $object
	 */
	function thewire_tools_create_object_event_handler($event, $type, $object) {
		
		if (elgg_instanceof($object, "object", "thewire")) {
			//send out notification to users mentioned in a wire post
			$usernames = array();
			
			if (preg_match_all("/\@([A-Za-z0-9\_\.\-]+)/i", $object->description, $usernames)) {
				$usernames = array_unique($usernames[0]);
				
				foreach ($usernames as $username) {
					$username = str_ireplace("@", "", $username);
					
					if (($user = get_user_by_username($username)) && ($user->getGUID() != $object->getOwnerGUID())) {
						if (elgg_get_plugin_user_setting("notify_mention", $user->getGUID(), "thewire_tools") == "yes") {
							$subject = elgg_echo("thewire_tools:notify:mention:subject");
							$message = elgg_echo("thewire_tools:notify:mention:message", array(
												$user->name,
												$object->getOwnerEntity()->name,
												elgg_get_site_url() . "thewire/search/@" . $user->username));
							
							notify_user($user->getGUID(), $object->getOwner(), $subject, $message);
						}
					}
				}
			}
		}
	}
