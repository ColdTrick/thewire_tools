<?php 

	function thewire_tools_create_object_event_handler($event, $type, $object){
		global $CONFIG;
		
		if(!empty($object) && ($object instanceof ElggObject) && ($object->getSubtype() == "thewire")){
			// find usernames
			$usernames = array();
			
			if(preg_match_all("/\@([A-Za-z0-9\_\.\-]+)/i", $object->description, $usernames)){
				$usernames = array_unique($usernames[0]);
				array_walk($usernames, "thewire_tools_array_cleanup");
				
				foreach($usernames as $username){
					if(($user = get_user_by_username($username)) && ($user->getGUID() != $object->getOwner())){
						if(get_plugin_usersetting("notify_mention", $user->getGUID(), "thewire_tools") == "yes"){
							$subject = elgg_echo("thewire_tools:notify:mention:subject");
							$message = sprintf(elgg_echo("thewire_tools:notify:mention:message"), 
												$user->name, 
												$object->getOwnerEntity()->name,
												$CONFIG->wwwroot . "pg/thewire/search/@" . $user->username);
							
							notify_user($user->getGUID(), $object->getOwner(), $subject, $message);
						}
					}
				}
			}
			
			// find hashtags
			$hashtags = array();
			
			if(preg_match_all("/\#([A-Za-z0-9\_\.\-]+)/i", $object->description, $hashtags)){
				$hashtags = array_unique($hashtags[0]);
				array_walk($hashtags, "thewire_tools_array_cleanup", "#"); 
				
				$object->tags = $hashtags;
			}
		}
	}


