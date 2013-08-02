<?php 

	$plugin = $vars["entity"];
	
	$noyes_options = array(
		"no" => elgg_echo("option:no"),
		"yes" => elgg_echo("option:yes")
	);	
		
	echo "<h4>".elgg_echo("thewire_tools:general_settings")."</h4>";
	
	// extend widgets
	echo "<div>";
	echo elgg_echo("thewire_tools:settings:extend_widgets");
	echo "&nbsp;" . elgg_view("input/dropdown", array("name" => "params[extend_widgets]", "value" => $plugin->extend_widgets, "options_values" => array_reverse($noyes_options, true)));
	echo "</div>";
		
	echo "<h4>".elgg_echo("thewire_tools:activity_settings")."</h4>";

	// extend widgets
	echo "<div>";
	echo elgg_echo("thewire_tools:settings:extend_activity");
	echo "&nbsp;" . elgg_view("input/dropdown", array("name" => "params[extend_activity]", "value" => $plugin->extend_activity, "options_values" => $noyes_options));
	echo "</div>";	
	
	// textarea label
	echo "<div>";
	echo elgg_echo("thewire_tools:settings:textarea_label")."*";
	echo "&nbsp;" . elgg_view("input/text", array("name" => "params[textarea_label]", "value" => $plugin->textarea_label));
	echo "<br>";
	echo "*".elgg_echo("thewire_tools:settings:textarea_label_hint")." (".elgg_echo("thewire_tools:settings:default_textarea_label").")";
	echo "</br>";
	echo "</div>";	
	
	echo "<h4>".elgg_echo("thewire_tools:group_settings")."</h4>";
	
	// enable group support
	echo "<div>";
	echo elgg_echo("thewire_tools:settings:enable_group");
	echo "&nbsp;" . elgg_view("input/dropdown", array("name" => "params[enable_group]", "value" => $plugin->enable_group, "options_values" => $noyes_options));
	echo "</div>";	
	
	// extend widgets
	echo "<div>";
	echo elgg_echo("thewire_tools:settings:extend_group_activity");
	echo "&nbsp;" . elgg_view("input/dropdown", array("name" => "params[extend_group_activity]", "value" => $plugin->extend_group_activity, "options_values" => $noyes_options));
	echo "</div>";		
	
	// textarea label
	echo "<div>";
	echo elgg_echo("thewire_tools:settings:group_textarea_label")."**";
	echo "&nbsp;" . elgg_view("input/text", array("name" => "params[group_textarea_label]", "value" => $plugin->group_textarea_label));
	echo "<br>";
	echo "**".elgg_echo("thewire_tools:settings:group_textarea_label_hint")." (".elgg_echo("thewire_tools:settings:default_group_textarea_label").")";
	echo "</br>";
	echo "</div>";

	echo "<h4>".elgg_echo("thewire_tools:menu_settings")."</h4>";
	
	// enable/disable site menu item for The Wire
	echo "<div>";
	echo elgg_echo("thewire_tools:settings:enable_site_menu_item");
	echo "&nbsp;" . elgg_view("input/dropdown", array("name" => "params[enable_site_menu_item]", "value" => $plugin->enable_site_menu_item, "options_values" => $noyes_options));
	echo "</div>";
	
	// enable/disable group menu item for The Wire
	echo "<div>";
	echo elgg_echo("thewire_tools:settings:enable_group_menu_item");
	echo "&nbsp;" . elgg_view("input/dropdown", array("name" => "params[enable_group_menu_item]", "value" => $plugin->enable_group_menu_item, "options_values" => $noyes_options));
	echo "</div>";	
