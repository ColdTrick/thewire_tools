<?php
	
	$widget = $vars["entity"];

	$num_display = (int) $widget->num_display;
	$owner = $widget->owner;
	$filter = $widget->filter;
	
	$error = false;
	
	if ($num_display < 1) {
		$num_display = 4;
	}
	
	$options = array(
		"type" => "object", 
		"subtype" => "thewire", 
		"limit" => $num_display, 
		"full_view" => false, 
		"pagination" => false
	);
	
	switch($owner){
		case "friends":
			// get users friends
			$friends_options = array(
				"type" => "user",
				"limit" => false,
				"relationship" => "friend",
				"relationship_guid" => $widget->getOwner(),
				"joins" => array("JOIN " . $vars["config"]->dbprefix . "entity_relationships r2 ON r2.guid_one = e.guid"),
				"wheres" => array("(r2.guid_two = " . $vars["config"]->site_guid . " AND r2.relationship = 'member_of_site')"),
				"site_guids" => false
			);
			
			if($friends = elgg_get_entities_from_relationship($friends_options)){
				$guids = array();
				
				foreach($friends as $friend){
					$guids[] = $friend->getGUID();
				}
				
				$options["container_guids"] = $guids;
				$more_url = $vars["url"] . "pg/thewire/friends/" . $widget->getOwnerEntity()->username;
			} else {
				$error = true;
			}
			break;
		case "all":
			// show all posts
			$more_url = $vars["url"] . "pg/thewire/all";
			break;
		default:
			$options["container_guid"] = $widget->getOwner();
			$more_url = $vars["url"] . "pg/thewire/owner/" . $widget->getOwnerEntity()->username;
		break;
	}
	
	
	if(empty($error) && !empty($filter)){
		$filters = string_to_tag_array($filter);
		array_walk($filters, "sanitise_string");
		
		$options["joins"] = array("JOIN " . $vars["config"]->dbprefix . "objects_entity oe ON oe.guid = e.guid");
		$options["wheres"] = array("(oe.description LIKE '%" . implode("%' OR oe.description LIKE '%", $filters) . "%')");
	}
	
	if (empty($error) && ($content = elgg_list_entities($options))) {
		echo $content;
		
		echo "<div class='widget_more_wrapper'>";
		echo elgg_view("output/url", array("href" => $more_url,
											"text" => elgg_echo("thewire:moreposts")));
		echo "</div>";
	} else {
		echo "<div class='widget_more_wrapper'>";
		echo elgg_echo("thewire_tools:no_result");
		echo "<div>";
	}
