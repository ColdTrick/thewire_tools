<?php 

	$group_guid = (int) get_input("group_guid", 0);
	
	$entities_only = (int) get_input("entities_only", 0);
	
	$options = array(
		'types' => 'object', 
		'subtypes' => 'thewire',
		'offset' => (int) max(get_input('offset', 0), 0),
		'limit' => (int) max(get_input('limit', 10), 0),
		'pagination' => FALSE,
		'container_guid' => $group_guid
	);
	
	if(($group = get_entity($group_guid)) && ($group instanceof ElggGroup)){
		// check if The Wire is enabled
		if($group->thewire_enable == "no"){
			register_error(elgg_echo("thewire_tools:groups:error:not_enabled"));
			forward(REFERER);
		}
		
		// set page owner
		set_page_owner($group->getGUID());
		set_context("groups");
		
		// prevent unauthorized access
		if(!$entities_only){
			group_gatekeeper();
		} elseif(!$group->isMember()){
			exit();
		}
		
		$entities_list = elgg_list_entities($options);
	
		$options["count"] = TRUE;
		$entities_count = elgg_get_entities($options);
		
		$result .= $entities_list;
		
		if(($entities_count - ($options['offset'] + $options['limit'])) > 0){
			$result .= elgg_view("thewire_tools/pagination", array("options" => $options));
		} else {
			if($entities_count == 0){
				$result .= elgg_view("page_elements/contentwrapper", array("body" => elgg_echo("notfound")));
			}
		}
		
		if($entities_only){
			echo $result;
		} else {
		    // build page elements
			$title_text = elgg_echo("thewire_tools:group:title");
			$title = elgg_view_title($title_text);
			
			if($group->isMember()){
				$add = elgg_view("thewire/forms/add", array("access_id" => $group->group_acl));
			}
			
			// build page
			$body = $title . $add . $result;
			
			// draw page
			page_draw($title_text, elgg_view_layout("two_column_left_sidebar", "", $body));
		}
		
	} else {
		if(!$entities_only){
			forward();
		}
	}
	exit();
