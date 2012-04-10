<?php 

	$query = get_input("query", get_input("q"));
	
	$options = array(
		'types' => 'object', 
		'subtypes' => 'thewire',
		'offset' => (int) max(get_input('offset', 0), 0),
		'limit' => (int) max(get_input('limit', 10), 0),
		'pagination' => true
	);
	
	elgg_push_breadcrumb(elgg_echo('thewire'), "thewire/all");
	elgg_push_breadcrumb(elgg_echo("thewire_tools:search:title:no_query"));
	
	if(!empty($query)){
		$options["joins"] = array("JOIN " . elgg_get_config("dbprefix") . "objects_entity oe ON e.guid = oe.guid");
		
		if($where_options = explode(" ", $query)){
			$wheres = array();
			foreach($where_options as $wo){
				$wheres[] = "oe.description LIKE '%" . sanitise_string($wo) . "%'";
			}
			
			if(!empty($wheres)){
				$options["wheres"] = "(" . implode(" AND ", $wheres) . ")";
			}
		}
		
		if($entities_list = elgg_list_entities($options)){
			$result = $entities_list;
		} else {
			$result = elgg_echo("notfound");
		}
				
		// set title
		$title_text = elgg_echo("thewire_tools:search:title", array($query));
	} else {
		$title_text = elgg_echo("thewire_tools:search:title:no_query");
		$result = elgg_echo("thewire_tools:search:no_query");
	}
	
	$form = elgg_view_form("thewire/search", array("id" => "thewire_tools_search_form", "action" => "thewire/search", "disable_security" => true, "method" => "GET") ,array("query" => $query));
    $body = elgg_view_layout("one_sidebar", array("title" => $title_text,"content" => $form . $result));
	
	// Display page
	echo elgg_view_page($title_text,$body);
