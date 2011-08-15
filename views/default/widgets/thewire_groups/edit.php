<?php 

	$widget = $vars["entity"];
	
	$count = $widget->wire_count;
	if(empty($count)){
		$count = 5;
	}
	
	echo "<div>" . elgg_echo("widgets:thewire_groups:settings:count") . "</div>";
	echo elgg_view("input/pulldown", array("internalname" => "params[wire_count]", "options" => range(1, 10), "value" => $count));
