<?php 

/**
 * This functions performs actions when a wire post is created
 * 
 * @param unknown_type $event
 * @param unknown_type $type
 * @param unknown_type $object
 */
function thewire_tools_create_object_event_handler($event, $type, $object){
	if(elgg_instanceof($object, "object", "thewire")){
		//send out notification to users mentioned in a wire post
		$usernames = array();
		
		if(preg_match_all("/\@([A-Za-z0-9\_\.\-]+)/i", $object->description, $usernames)){
			$usernames = array_unique($usernames[0]);
			
			foreach($usernames as $username){
				$username = str_ireplace("@", "", $username);
				
				if(($user = get_user_by_username($username)) && ($user->getGUID() != $object->getOwnerGUID())){
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
		
		// update access and container guid if needed
		if(elgg_get_plugin_setting("enable_group", "thewire_tools") == "yes"){
			$access_id = get_input("access_id",false);
			if($access_id !== false){
				$access_id = sanitize_int($access_id);
				// try to find a group with access_id
				$group_options = array(
							"type" => "group",
							"limit" => 1,
							"metadata_name_value_pairs" => array("group_acl" => $access_id)
				);
				
				if($groups = elgg_get_entities_from_metadata($group_options)){
					$group = $groups[0];
						
					if($group->thewire_enable == "no"){
						// not allowed to post in this group
						register_error(elgg_echo("thewire_tools:groups:error:not_enabled"));
						
						// let creation of object fail
						return false;
					} else {
						$container_guid = $group->getGUID();
					}
				}
				
				if($container_guid){				
					$object->container_guid = $container_guid;
				}
				
				$object->access_id = $access_id;
				
				$object->save();
			}
		}
	}
}
