<?php 

	require_once(dirname(__FILE__) . "/lib/functions.php");
	require_once(dirname(__FILE__) . "/lib/events.php");
	
	global $THEWIRE_TOOLS_ENABLE_GROUP;
	
	function thewire_tools_init(){
		global $THEWIRE_TOOLS_ENABLE_GROUP;
		
		if(is_plugin_enabled("thewire")){
			// extend CSS
			elgg_extend_view("css", "thewire_tools/css");
			elgg_extend_view("js/initialise_elgg", "thewire_tools/js");
				
			// overrule thewire add action
			register_action("thewire/add", false, dirname(__FILE__) . "/actions/thewire/add.php");
			register_action("thewire/delete", false, dirname(__FILE__) . "/actions/thewire/delete.php");
				
			// extend thewire pagehandler
			thewire_tools_extend_page_handler("thewire", "thewire_tools_page_handler");
				
			// overrule url handler
			register_entity_url_handler("thewire_tools_url_handler", "object", "thewire");
				
			if(get_plugin_setting("enable_group", "thewire_tools") == "yes"){
				$THEWIRE_TOOLS_ENABLE_GROUP = true;
				// add widget (for Widget Manager only)
				add_widget_type("thewire_groups", elgg_echo("widgets:thewire_groups:title"), elgg_echo("widgets:thewire_groups:description"), "groups");
				
				if(is_callable("add_widget_title_link")){
					add_widget_title_link("thewire_groups", "[BASEURL]pg/thewire/groups/[GUID]");
				}
			}
			
			// adds wire post form to the wire widget
			if(isloggedin() && (get_plugin_setting("extend_widgets", "thewire_tools") != "no")){
				elgg_extend_view("widgets/thewire/view", "thewire_tools/thewire_post", 400);
				elgg_extend_view("widgets/index_thewire/view", "thewire_tools/thewire_post", 400);
			}
			
			// add some extra widgets (for Widget Manager only)
			add_widget_type("index_thewire", elgg_echo("widgets:index_thewire:title"), elgg_echo("widgets:index_thewire:description"), "index", true);
			add_widget_type("thewire_post", elgg_echo("widgets:thewire_post:title"), elgg_echo("widgets:thewire_post:description"), "index,dashboard", false);
			if(is_callable("add_widget_title_link")){
				add_widget_title_link("index_thewire", "[BASEURL]pg/thewire/all/");
				add_widget_title_link("thewire_post", "[BASEURL]pg/thewire/all/");
			}
		}
	}
	
	function thewire_tools_pagesetup(){
		global $CONFIG, $THEWIRE_TOOLS_ENABLE_GROUP;
	
		if(is_plugin_enabled("thewire")){
			$page_owner = page_owner_entity();
			$user = get_loggedin_user();
			$context = get_context();
				
			if(!empty($user) && ($page_owner instanceof ElggGroup) && ($context == "groups")){
				if($THEWIRE_TOOLS_ENABLE_GROUP){
					add_submenu_item(elgg_echo("thewire_tools:menu:group"), $CONFIG->wwwroot . "pg/thewire/group/" . $page_owner->getGUID());
				}
			}
			
			if(!empty($user) && ($context == "thewire")){
				add_submenu_item(elgg_echo("thewire_tools:menu:mentions"), $CONFIG->wwwroot . "pg/thewire/search/@" . $user->username);
			}
			
			if($context == "thewire"){
				add_submenu_item(elgg_echo("search"), $CONFIG->wwwroot . "pg/thewire/search/");
			}
				
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
	}
	
	function thewire_tools_url_handler($entity){
		global $CONFIG;
		
		if($entity->getContainerEntity() instanceof ElggGroup){
			$entity_url = $CONFIG->url . "pg/thewire/group/" . $entity->getContainer();
		} else {
			$entity_url = $CONFIG->url . "pg/thewire/owner/" . $entity->getOwnerEntity()->username;
		}
		
		return $entity_url;
	}

	// register default Elgg events
	register_elgg_event_handler("init", "system", "thewire_tools_init");
	register_elgg_event_handler("pagesetup", "system", "thewire_tools_pagesetup");

	// register events
	register_elgg_event_handler("create", "object", "thewire_tools_create_object_event_handler");
	