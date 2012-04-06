<?php 

	require_once(dirname(__FILE__) . "/lib/functions.php");
	require_once(dirname(__FILE__) . "/lib/events.php");
	
	global $THEWIRE_TOOLS_ENABLE_GROUP;
	
	function thewire_tools_init(){
		global $THEWIRE_TOOLS_ENABLE_GROUP;
		
		// extend CSS
		elgg_extend_view("css/elgg", "thewire_tools/css");
		elgg_extend_view("js/elgg", "thewire_tools/js");
			
		// overrule thewire add action
		elgg_register_action("thewire/add", dirname(__FILE__) . "/actions/thewire/add.php");
		elgg_register_action("thewire/delete", dirname(__FILE__) . "/actions/thewire/delete.php");
			
		// extend thewire pagehandler
		thewire_tools_extend_page_handler("thewire", "thewire_tools_page_handler");
			
		// overrule url handler
		elgg_register_entity_url_handler("thewire_tools_url_handler", "object", "thewire");
			
		if(elgg_get_plugin_setting("enable_group", "thewire_tools") == "yes"){
			$THEWIRE_TOOLS_ENABLE_GROUP = true;
			// add widget (for Widget Manager only)
			elgg_register_widget_type("thewire_groups", elgg_echo("widgets:thewire_groups:title"), elgg_echo("widgets:thewire_groups:description"), "groups", true);
			
			if(is_callable("widget_manager_add_widget_title_link")){
				widget_manager_add_widget_title_link("thewire_groups", "[BASEURL]pg/thewire/groups/[GUID]");
			}
			
			// add group tool option
			add_group_tool_option("thewire", elgg_echo("thewire_tools:groups:tool_option"), true);
		}
		
		// adds wire post form to the wire widget
		if(elgg_is_logged_in() && (elgg_get_plugin_setting("extend_widgets", "thewire_tools") != "no")){
			elgg_extend_view("widgets/thewire/view", "thewire_tools/thewire_post", 400);
			elgg_extend_view("widgets/index_thewire/view", "thewire_tools/thewire_post", 400);
		}
		
		// add some extra widgets (for Widget Manager only)
		elgg_register_widget_type("index_thewire", elgg_echo("widgets:index_thewire:title"), elgg_echo("widgets:index_thewire:description"), "index", true);
		elgg_register_widget_type("thewire_post", elgg_echo("widgets:thewire_post:title"), elgg_echo("widgets:thewire_post:description"), "index,dashboard", false);
		if(is_callable("widget_manager_add_widget_title_link")){
			widget_manager_add_widget_title_link("index_thewire", "[BASEURL]thewire/all/");
			widget_manager_add_widget_title_link("thewire_post", "[BASEURL]thewire/all/");
		}
	}
	
	function thewire_tools_pagesetup(){
		$page_owner = elgg_get_page_owner_entity();
		$user = elgg_get_logged_in_user_entity();

		if(!empty($user) && ($page_owner instanceof ElggGroup) && elgg_in_context("groups")){
			if((elgg_get_plugin_setting("enable_group", "thewire_tools") == "yes") && ($page_owner->thewire_enable != "no")){
				add_submenu_item(elgg_echo("thewire_tools:menu:group"), "thewire/group/" . $page_owner->getGUID());
			}
		}
		
		// cleanup group widget
		if(($page_owner instanceof ElggGroup) && ($page_owner->thewire_enable == "no")){
			elgg_unregister_widget_type("thewire_groups");
		}
		
		if(!empty($user) && elgg_in_context("thewire")){
			add_submenu_item(elgg_echo("thewire_tools:menu:mentions"), "thewire/search/@" . $user->username);
		}
		
		if(elgg_in_context("thewire")){
			add_submenu_item(elgg_echo("search"), "thewire/search/");
		}
	}
	
	function thewire_tools_page_handler($page, $handler){
		
		switch($page[0]){
			case "group":
				if(!empty($page[1])){
					set_input("group_guid", $page[1]);
						
					if(!empty($page[2])){
						set_input("wire_username", $page[2]);
					}
						
					include(dirname(__FILE__) . "/pages/group.php");
					break;
				}
			case "add":
			case "reply":
				if(isset($page[1]) && is_numeric($page[1])){
					set_input("reply_guid", $page[1]);
				} elseif(isset($page[1])){
					set_input("wire_username", $page[1]);
				}
				include(dirname(__FILE__) . "/pages/add.php");
				break;
			case "search":
				if(isset($page[1])){
					set_input("query", $page[1]);
				}
				
				include(dirname(__FILE__) . "/pages/search.php");
				break;
			case "friends":
				if(isset($page[1])){
					set_input("username", $page[1]);
				}
				include(dirname(__FILE__) . "/pages/friends.php");
				break;
			case "all":
				if(isset($page[1])){
					set_input("username", $page[1]);
				}
				include(dirname(__FILE__) . "/pages/everyone.php");
				break;
			case "owner":
				if(isset($page[1])){
					set_input("username", $page[1]);
				}
				include(dirname(__FILE__) . "/pages/index.php");
				break;
			case "autocomplete":
				include(dirname(__FILE__) . "/procedures/autocomplete.php");
				break;
			case "conversation":
				if(isset($page[1])){
					set_input("guid", $page[1]);
				}
				include(dirname(__FILE__) . "/procedures/conversation.php");
				break;
			default:
				return thewire_tools_fallback_page_handler($page, $handler);
			break;
		}
		return true;
	}
	
	function thewire_tools_url_handler($entity){
		if($entity->getContainerEntity() instanceof ElggGroup){
			$entity_url = elgg_get_site_url(). "thewire/group/" . $entity->getContainer();
		} else {
			$entity_url = elgg_get_site_url() . "thewire/owner/" . $entity->getOwnerEntity()->username;
		}
		
		return $entity_url;
	}

	// register default Elgg events
	elgg_register_event_handler("init", "system", "thewire_tools_init");
	elgg_register_event_handler("pagesetup", "system", "thewire_tools_pagesetup");

	// register events
	elgg_register_event_handler("create", "object", "thewire_tools_create_object_event_handler");
	