<?php

	// Load Elgg engine
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
	
	$entities_only = (int) get_input("entities_only", 0);
	
	$options = array(
		'types' => 'object', 
		'subtypes' => 'thewire',
		'offset' => (int) max(get_input('offset', 0), 0),
		'limit' => (int) max(get_input('limit', 10), 0),
		'pagination' => FALSE
	);
		
	$entities_list = elgg_list_entities($options);
	
	$options["count"] = TRUE;
	$entities_count = elgg_get_entities($options);
	
	if(!$entities_only){
		$result = elgg_view_title(elgg_echo("thewire:everyone"));
		
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
		page_draw(elgg_echo('thewire:everyone'),$body);
	}
	