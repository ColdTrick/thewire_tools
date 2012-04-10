<?php 

group_gatekeeper();

$group_guid = sanitize_int(get_input("group_guid", 0));
$entities_only = sanitize_int(get_input("entities_only", 0));

$options = array(
	'types' => 'object', 
	'subtypes' => 'thewire',
	'offset' => (int) max(get_input('offset', 0), 0),
	'limit' => (int) max(get_input('limit', 10), 0),
	'pagination' => true,
	'container_guid' => $group_guid
);

if(($group = get_entity($group_guid)) && ($group instanceof ElggGroup)){
	// check if The Wire is enabled
	if($group->thewire_enable == "no"){
		register_error(elgg_echo("thewire_tools:groups:error:not_enabled"));
		forward($group->getURL());
	}
	
	elgg_push_breadcrumb(elgg_echo('thewire'), "thewire/all");
	elgg_push_breadcrumb($group->name);
	
	if($entities_list = elgg_list_entities($options)){
		$result = $entities_list;
	} else {
		$result = elgg_echo("notfound");
	}
	
    // build page elements
	$title_text = elgg_echo("thewire_tools:group:title");
	
	if($group->isMember()){
		$add = elgg_view_form("thewire/add");
	}
	
	$body = elgg_view_layout("one_sidebar", array("title" => $title_text,"content" => $add . $result));
	
	// Display page
	echo elgg_view_page($title_text,$body);
	
} else {
	forward("thewire/all");
}
