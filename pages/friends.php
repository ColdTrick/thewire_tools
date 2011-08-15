<?php

	// Get the page's owner
	$page_owner = page_owner_entity();
	if ($page_owner === false || is_null($page_owner)) {
		$page_owner = get_loggedin_user();
		set_page_owner($page_owner->getGUID());
	}
	
	$entities_only = (int) get_input("entities_only", 0);
	
	if ($friends = get_user_friends($page_owner->getGUID(), "", 999999, 0)) {
		$friendguids = array();
		foreach($friends as $friend) {
			$friendguids[] = $friend->getGUID();
		}
	}
	
	$options = array(
		'types' => 'object', 
		'subtypes' => 'thewire',
		'offset' => (int) max(get_input('offset', 0), 0),
		'limit' => (int) max(get_input('limit', 10), 0),
		'pagination' => FALSE,
		'owner_guids' => $friendguids,
		'container_guids' => $friendguids
	);
		
	$entities_list = elgg_list_entities($options);
	
	$options["count"] = TRUE;
	$entities_count = elgg_get_entities($options);
	
	if(!$entities_only){
		$title = sprintf(elgg_echo("thewire:friends"), $page_owner->name);
		$result = elgg_view_title($title);
		
		//add form
		if (isloggedin()) {
			$result .= elgg_view("thewire/forms/add");
		}
	}
	
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
	    $body = elgg_view_layout("two_column_left_sidebar", '', $result);
		
		// Display page
		page_draw($title,$body);
	}
	