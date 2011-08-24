<?php 

	$widget = $vars["entity"];
	
	// backup context and set
	$old_context = get_context();
	set_context("search");
	
	// get widget settings
	$count = (int) $widget->wire_count;
	$filter = $widget->filter;
	
	if($count < 1){
		$count = 8;
	}

	$options = array(
		"type" => "object",
		"subtype" => "thewire",
		"limit" => $count,
		"full_view" => false,
		"pagination" => false,
		"view_type_toggle" => false
	);
	
	if(!empty($filter)){
		$filters = string_to_tag_array($filter);
		array_walk($filters, "sanitise_string");
		
		$options["joins"] = array("JOIN " . $vars["config"]->dbprefix . "objects_entity oe ON oe.guid = e.guid");
		$options["wheres"] = array("(oe.description LIKE '%" . implode("%' OR oe.description LIKE '%", $filters) . "%')");
	}
	
	if($content = elgg_list_entities($options)){
		echo $content;
	} else {
		echo "<div class='widget_more_wrapper'>";
		echo elgg_echo("thewire_tools:no_result");
		echo "</div>";
	}

	// reset context
	set_context($old_context);
?>