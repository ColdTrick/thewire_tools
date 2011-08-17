<?php

	$widget = $vars["entity"];
	
	$num_display = (int) $widget->num_display;
	if($num_display < 1){
		$num_display = 4;
	}
	
	$owner_options = array(
		"mine" => elgg_echo("widgets:thewire:owner:mine"),
		"friends" => elgg_echo("widgets:thewire:owner:friends"),
		"all" => elgg_echo("widgets:thewire:owner:all")
	);
	
?>
<p>
	<?php 
		echo elgg_echo("thewire:num");
		echo "&nbsp;" . elgg_view("input/pulldown", array("internalname" => "params[num_display]", "options" => range(1, 10), "value" => $num_display));
	?>
</p>
<p>
	<?php 
		echo elgg_echo("widgets:thewire:owner");
		echo "&nbsp;" . elgg_view("input/pulldown", array("internalname" => "params[owner]", "options_values" => $owner_options, "value" => $widget->owner));
	?>
</p>
<p>
	<?php 
		echo elgg_echo("widgets:thewire:filter");
		echo "&nbsp;" . elgg_view("input/text", array("internalname" => "params[filter]", "value" => $widget->filter));
	?>
</p>