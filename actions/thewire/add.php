<?php

	/**
	 * Elgg thewire: add shout action
	 *
	 * @package Elggthewire
	 */

	global $THEWIRE_TOOLS_ENABLE_GROUP;

	// Make sure we're logged in (send us to the front page if not)
	gatekeeper();
	
	$user = get_loggedin_user();
	
	// Get input data
	$body = get_input("note");
	$method = get_input("method");
	$parent = (int) get_input("parent", 0);
	$reply_guid = (int) get_input("reply_guid", 0);
	$conversation = (int) get_input("conversation", 0);
	$forward_url = get_input("forward_url", REFERER);
	
	$error = false;
	
	// make access_id
	if($THEWIRE_TOOLS_ENABLE_GROUP){
		$access_id = (int) get_input("access_id");
	} else {
		$access_id = get_default_access();
	}
	
	// Private wire messages are pointless
	if ($access_id == ACCESS_PRIVATE) {
		$access_id = ACCESS_LOGGED_IN; 
	}
	
	$container_guid = $user->getGUID();
	
	if($THEWIRE_TOOLS_ENABLE_GROUP){
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
				$error = true;
				register_error(elgg_echo("thewire_tools:groups:error:not_enabled"));
			} else {
				$container_guid = $group->getGUID();
			}
		}
	}
	
	if(!$error){
		// Make sure the body isn't blank
		if (!empty($body)) {
			if (thewire_tools_save_post($body, $access_id, $parent, $method, $container_guid, $reply_guid, $conversation)) {
				system_message(elgg_echo("thewire:posted"));
			} else {
				register_error(elgg_echo("thewire:error"));
			}
		} else {
			register_error(elgg_echo("thewire:blank"));
		}
	}
	
	// Forward
	forward($forward_url);
