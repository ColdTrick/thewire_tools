<?php 

function thewire_tools_create_object_event_handler($event, $type, $object){
	if(elgg_instanceof($object, "object", "thewire")){
		// find usernames
		$usernames = array();
		
		if(preg_match_all("/\@([A-Za-z0-9\_\.\-]+)/i", $object->description, $usernames)){
			$usernames = array_unique($usernames[0]);
			array_walk($usernames, "thewire_tools_array_cleanup");
			
			foreach($usernames as $username){
				if(($user = get_user_by_username($username)) && ($user->getGUID() != $object->getOwner())){
					if(elgg_get_plugin_user_setting("notify_mention", $user->getGUID(), "thewire_tools") == "yes"){
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
		
		// find hashtags
		$hashtags = array();
		
		if(preg_match_all("/\#([A-Za-z0-9\_\.\-]+)/i", $object->description, $hashtags)){
			$hashtags = array_unique($hashtags[0]);
			array_walk($hashtags, "thewire_tools_array_cleanup", "#"); 
			
			$object->tags = $hashtags;
		}
	}
}
