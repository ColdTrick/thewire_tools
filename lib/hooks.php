<?php

/**
 * Extends thewire pagehandler with some extra pages
 * 
 * @param unknown_type $hook_name
 * @param unknown_type $entity_type
 * @param unknown_type $return
 * @param unknown_type $params
 */
function thewire_tools_route_thewire($hook_name, $entity_type, $return, $params){
	$page = elgg_extract("segments", $return);
	if(is_array($page)){
		switch($page[0]){
			case "group":
				if(!empty($page[1])){
					set_input("group_guid", $page[1]); // @todo is this still needed or replace with page_owner in page
						
					if(!empty($page[2])){
						set_input("wire_username", $page[2]); // @todo is this still needed?
					}
						
					$include_file = "pages/group.php";
					break;
				}
			case "tag":
			case "search":
				if(isset($page[1])){
					if($page[0] == "tag"){
						set_input("query", "#" . $page[1]);
					} else {
						set_input("query", $page[1]);
					}
				}
				
				$include_file =  "pages/search.php";
				break;
			case "autocomplete":
				$include_file = "procedures/autocomplete.php";
				break;
			case "conversation":
				if(isset($page[1])){
					set_input("guid", $page[1]);
				}
				$include_file = "procedures/conversation.php";
				break;
			
		}
		if(!empty($include_file)){
			include(elgg_get_plugins_path() . "thewire_tools/" . $include_file);
			return false;
		}
		
	}
}

/**
 * Optionally extend the group owner block with a link to the wire posts of the group
 * 
 * @param unknown_type $hook_name
 * @param unknown_type $entity_type
 * @param unknown_type $return
 * @param unknown_type $params
 * @return ElggMenuItem
 */
function thewire_tools_owner_block_menu($hook_name, $entity_type, $return, $params){
	$group = elgg_extract("entity", $params);
	if (elgg_instanceof($group, 'group') && $group->thewire_enable != "no") {
		$url = "thewire/group/{$group->getGUID()}";
		$item = new ElggMenuItem('thewire', elgg_echo('thewire_tools:group:title'), $url);
		$return[] = $item;
	} 
	
	return $return;
}

/**
 * Provide a custom access pulldown for use on personal wire posts
 * 
 * @param unknown_type $hook_name
 * @param unknown_type $entity_type
 * @param unknown_type $return
 * @param unknown_type $params
 * @return unknown
 */
function thewire_tools_access_write_hook($hook_name, $entity_type, $return, $params){
	if(elgg_in_context("thewire") && ($user = elgg_get_logged_in_user_entity())){
		if(is_array($return)){
			unset($return[ACCESS_PRIVATE]);
			unset($return[ACCESS_FRIENDS]);
			
			$options = array(
				"type" => "group",
				"limit" => false,
				"relationship" => "member",
				"relationship_guid" => $user->getGUID()
			);
			
			if($groups = elgg_get_entities_from_relationship($options)){
				foreach($groups as $group){
					if($group->thewire_enable !== "no"){
						$return[$group->group_acl] = $group->name;
					}
				}
			}
		}		
	}
	
	return $return;
}

/**
 * removes thread link from thewire entity menu if there is no conversation
 * 
 * @param unknown_type $hook_name
 * @param unknown_type $entity_type
 * @param unknown_type $return
 * @param unknown_type $params
 */
function thewire_tools_register_entity_menu_items($hook_name, $entity_type, $return, $params){
	$entity = elgg_extract('entity', $params, false);
	
	if($entity && elgg_instanceof($entity, "object", "thewire")){
		if(is_array($return)){
			foreach($return as $index => $menu_item){
				if($menu_item->getName() == "thread"){
					if(!($entity->countEntitiesFromRelationship("parent") || $entity->countEntitiesFromRelationship("parent", true))){
						unset($return[$index]);
					}
				}
			}
		}
		
		return $return;
	}
}

/**
 * Forwards thewire delete action back to referer
 * 
 * @param unknown_type $hook_name
 * @param unknown_type $entity_type
 * @param unknown_type $return
 * @param unknown_type $params
 * @return unknown
 */
function thewire_tools_forward_hook($hook_name, $entity_type, $return, $params){
	if(get_input("action") == "thewire/delete"){
		return $_SERVER['HTTP_REFERER'];
	}
}

/**
 * returns the correct widget title
 * 
 * @param unknown_type $hook_name
 * @param unknown_type $entity_type
 * @param unknown_type $return
 * @param unknown_type $params
 */
function thewire_tools_widget_title_url($hook_name, $entity_type, $return, $params){
	$result = $return;
	$widget = $params["entity"];
	if(empty($result) && ($widget instanceof ElggWidget)){
		switch($widget->handler) {
			case "thewire":
				$result = "/thewire/owner/" . $widget->getOwnerEntity()->username;
				break;
			case "index_thewire":
			case "thewire_post":
				$result = "/thewire/all";
				break;
			case "thewire_groups":
				$result = "/thewire/group/" . $widget->getOwnerGUID();
				break;
		}
	}
	return $result;
}