<?php 

	$plugin = $vars["entity"];
	
	$noyes_options = array(
		"no" => elgg_echo("option:no"),
		"yes" => elgg_echo("option:yes")
	);

	// enable group support
	echo "<div>";
	echo elgg_echo("thewire_tools:settings:enable_group");
	echo "&nbsp;" . elgg_view("input/pulldown", array("internalname" => "params[enable_group]", "value" => $plugin->enable_group, "options_values" => $noyes_options));
	echo "</div>";
	
	// extend widgets
	echo "<div>";
	echo elgg_echo("thewire_tools:settings:extend_widgets");
	echo "&nbsp;" . elgg_view("input/pulldown", array("internalname" => "params[extend_widgets]", "value" => $plugin->extend_widgets, "options_values" => array_reverse($noyes_options, true)));
	echo "</div>";
