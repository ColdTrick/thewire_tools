<?php 

	$query = get_input("query", get_input("q"));
	
	$entities_only = (int) get_input("entities_only", 0);
	
	$options = array(
		'types' => 'object', 
		'subtypes' => 'thewire',
		'offset' => (int) max(get_input('offset', 0), 0),
		'limit' => (int) max(get_input('limit', 10), 0),
		'pagination' => FALSE
	);
	
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
		
		$entities_list = elgg_list_entities($options);
		$result .= $entities_list;
		
		$options["count"] = TRUE;
		$entities_count = elgg_get_entities($options);
		
		if(($entities_count - ($options['offset'] + $options['limit'])) > 0){
			$result .= elgg_view("thewire_tools/pagination", array("options" => $options));
		} else {
			if($entities_count == 0){
				$result .= elgg_view("page_elements/contentwrapper", array("body" => elgg_echo("notfound")));
			}
		}
		
		// set title
		$title_text = elgg_echo("thewire_tools:search:title", array($query));
	} else {
		$title_text = elgg_echo("thewire_tools:search:title:no_query");
		$result = elgg_echo("thewire_tools:search:no_query");
	}
	
	if($entities_only){
		echo $result;	
	} else {
		$search = elgg_view("thewire_tools/forms/search", array("query" => $query));
		
	    $body = elgg_view_layout("one_sidebar", array("title" => $title_text,"content" => $search . $result));
		
		// Display page
		echo elgg_view_page($title_text,$body);
	}
