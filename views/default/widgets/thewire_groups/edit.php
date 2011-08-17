<?php 

	$widget = $vars["entity"];
	
	$count = (int) $widget->wire_count;
	if($count < 1){
		$count = 5;
	}
	
	echo "<div>";
	echo elgg_echo("widgets:thewire_groups:count");
	echo "&nbsp;" . elgg_view("input/pulldown", array("internalname" => "params[wire_count]", "options" => range(1, 10), "value" => $count));
	echo "</div>";
	
	echo "<div>";
	echo elgg_echo("widgets:thewire_groups:filter");
	echo "&nbsp;" . elgg_view("input/text", array("internalname" => "params[filter]", "value" => $widget->filter));
	echo "</div>";
