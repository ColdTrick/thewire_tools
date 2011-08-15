<?php 

	$form_data = elgg_view("input/text", array("internalname" => "q", "value" => $vars["query"]));
	$form_data .= "&nbsp;" . elgg_view("input/submit", array("value" => elgg_echo("search")));
	
	$form = elgg_view("input/form", array("body" => $form_data, 
											"action" => $vars["url"] . "pg/thewire/search/",
											"internalid" => "thewire_tools_search_form",
											"disable_security" => true,
											"method" => "GET"));
	
	echo elgg_view("page_elements/contentwrapper", array("body" => $form));
