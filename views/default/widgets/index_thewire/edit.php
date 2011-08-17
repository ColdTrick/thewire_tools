<?php 

	$widget = $vars["entity"];
	
	$count = (int) $widget->wire_count;
	if($count < 1){
		$count = 8;
	}

?>
<div><?php echo elgg_echo("widgets:index_thewire:wire_count"); ?></div>
<input type="text" name="params[wire_count]" value="<?php echo elgg_view("output/text", array("value" => $count)); ?>" size="4" maxlength="4" />

<div>
	<?php 
		echo elgg_echo("widgets:index_thewire:filter");
		echo "&nbsp;" . elgg_view("input/text", array("internalname" => "params[filter]", "value" => $widget->filter));
	?>
</div>