<?php 

	global $THEWIRE_TOOLS_PAGEHANDLER_BACKUP;
	$THEWIRE_TOOLS_PAGEHANDLER_BACKUP = array();
	
	function thewire_tools_extend_page_handler($handler, $function){
		global $CONFIG;
		global $THEWIRE_TOOLS_PAGEHANDLER_BACKUP;
	
		$result = false;
	
		if(!empty($handler) && !empty($function) && is_callable($function)){
			if(isset($CONFIG->pagehandler) && array_key_exists($handler, $CONFIG->pagehandler)){
				// backup original page handler
				$THEWIRE_TOOLS_PAGEHANDLER_BACKUP[$handler] = $CONFIG->pagehandler[$handler];
				// register new handler
				register_page_handler($handler, $function);
				$result = true;
			} else {
				register_page_handler($handler, $function);
				$result = true;
			}
		}
	
		return $result;
	}
	
	function thewire_tools_fallback_page_handler($page, $handler){
		global $THEWIRE_TOOLS_PAGEHANDLER_BACKUP;
	
		$result = false;
	
		if(!empty($handler)){
			if(array_key_exists($handler, $THEWIRE_TOOLS_PAGEHANDLER_BACKUP)){
				if(is_callable($THEWIRE_TOOLS_PAGEHANDLER_BACKUP[$handler])){
					$function = $THEWIRE_TOOLS_PAGEHANDLER_BACKUP[$handler];
						
					$result = $function($page, $handler);
						
					if($result !== false){
						$result = true;
					}
				}
			}
		}
	
		return $result;
	}

	function thewire_tools_save_post($post, $access_id, $parent = 0, $method = "site", $container_guid = 0, $reply_guid = 0, $conversation = 0){
		$user_guid = get_loggedin_userid();
	
		if(empty($container_guid)){
			$container_guid = $user_guid;
		}
	
		// Initialise a new ElggObject
		$thewire = new ElggObject();
	
		// Tell the system it's a thewire post
		$thewire->subtype = "thewire";
	
		// Set its owner to the current user
		$thewire->owner_guid = $user_guid;
		$thewire->container_guid = $container_guid;
	
		// For now, set its access to public (we'll add an access dropdown shortly)
		$thewire->access_id = $access_id;
	
		// Set its description appropriately
		$thewire->description = elgg_substr(strip_tags($post), 0, 160);
	
		// add some metadata
		$thewire->method = $method; //method, e.g. via site, sms etc
		$thewire->parent = $parent; //used if the note is a reply
		
		// log conversation
		if(!empty($reply_guid)){
			$thewire->reply_guid = $reply_guid;
		}
		if(!empty($conversation)){
			$thewire->conversation = $conversation;
		}
	
		//save
		$save = $thewire->save();
	
		if ($save) {
			add_to_river('river/object/thewire/create', 'create', $user_guid, $thewire->guid);
	
			// tweet
			$params = array(
					'plugin' => 'thewire',
					'message' => $thewire->description
			);
	
			trigger_plugin_hook('tweet', 'twitter_service', $params);
		}
	
		return $save;
	}
	
	function thewire_tools_array_cleanup(&$item, $key, $replace = "@"){
		$item = str_ireplace($replace, "", $item);
	}
	