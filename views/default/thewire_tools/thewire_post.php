<?php 

	$widget = $vars["entity"]; 

	if(elgg_is_logged_in()){	
		echo elgg_view_form("thewire/add"); 
	}