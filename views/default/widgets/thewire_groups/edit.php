<?php 

	$widget = $vars["entity"];
	
	$count = (int) $widget->wire_count;
	if($count < 1){
		$count = 5;
	}
	
	echo "<div>";
	echo elgg_echo("thewire:num");
	echo "&nbsp;" . elgg_view("input/dropdown", array("name" => "params[wire_count]", "options" => range(1, 10), "value" => $count));
	echo "</div>";
	
	echo "<div>";
	echo elgg_echo("widgets:thewire:filter");
	echo "&nbsp;" . elgg_view("input/text", array("name" => "params[filter]", "value" => $widget->filter));
	echo "</div>";
