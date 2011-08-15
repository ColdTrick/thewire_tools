<?php

	/**
	 * Elgg thewire add entry page
	 * 
	 * @package ElggTheWire
	 *
	 */

	// If we're not logged in, forward to the front page
	gatekeeper();
	
	// check if there is a conversation to show
	if($reply_guid = get_input("reply_guid")){
		if(($reply = get_entity($reply_guid)) && ($reply->getSubtype() == "thewire")){
			// set page_owner
			if($reply->getContainerEntity() instanceof ElggGroup){
				set_page_owner($reply->getContainer());
				set_context("groups");
				$access_id = $reply->getContainerEntity()->group_acl;
			}
			
			set_input("wire_username", $reply->getOwnerEntity()->username);
			
			if(!empty($reply->conversation)){
				$options = array(
					"type" => "object",
					"subtype" => "thewire",
					"limit" => false,
					"metadata_name_value_pairs" => array("conversation" => $reply->conversation),
					"wheres" => array("(e.guid <= " . $reply->getGUID() . ")")
				);
				
				if($entities = elgg_get_entities_from_metadata($options)){
					if($start = get_entity($reply->conversation)){
						$entities[] = $start;
					}
				} else {
					if($start = get_entity($reply->conversation)){
						$entities = array($start);
					}
				}
			} else {
				$entities = array($reply);
			}
		} else {
			$reply = null;
		}
	}
	
	if(!page_owner()){
		set_page_owner(get_loggedin_userid());
	}

	// build title
	$title = elgg_view_title(elgg_echo("thewire:add"));
	
	// build form
	$form = elgg_view("thewire/forms/add", array("reply" => $reply, "access_id" => $access_id));
	
	if(!empty($entities)){
		// set context to conversation mode
		$context = get_context();
		set_context("thewire_tools_conversation");
			
		$conversation = elgg_view_entity_list($entities, count($entities), 0, count($entities), false, false, false);
			
		// restore context
		set_context($context);
	}
	
    // build page data
    $page_data = $title . $form . $conversation;
		
	// Display page
	page_draw(elgg_echo("thewire:addpost"), elgg_view_layout("two_column_left_sidebar", "", $page_data));

