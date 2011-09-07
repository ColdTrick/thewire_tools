<?php 

	$widget = $vars["entity"];
	$group = $widget->getOwnerEntity();
	
	$count = (int) $widget->wire_count;
	$filter = $widget->filter;
	
	$error = false;
	
	if($count < 1){
		$count = 5;
	}
	
	$options = array(
		"type" => "object",
		"subtype" => "thewire",
		"limit" => $count,
		"container_guid" => $group->getGUID(),
		"pagination" => false,
		"full_view" => false
	);
	
	if(!empty($filter)){
		$filters = string_to_tag_array($filter);
		array_walk($filters, "sanitise_string");
	
		$options["joins"] = array("JOIN " . $vars["config"]->dbprefix . "objects_entity oe ON oe.guid = e.guid");
		$options["wheres"] = array("(oe.description LIKE '%" . implode("%' OR oe.description LIKE '%", $filters) . "%')");
	}
	
	if(!($list = elgg_list_entities($options))){
		$error = true;
		
		$list = "<div class='widget_more_wrapper'>";
		$list .= elgg_echo("thewire_tools:no_result");
		$list .= "<div>";
	}

	if($group->isMember()) {
		?>
		<div class="contentWrapper">
			<a href="javascript:void(0);" onclick="$(this).next().toggle();"><?php echo elgg_echo("thewire:newpost"); ?></a>
			<?php echo elgg_view("thewire/forms/add", array("access_id" => $group->group_acl, "widget" => true)); ?>
		</div>
		<style type="text/css">
			#widget<?php echo $widget->getGUID();?> .post_to_wire {
				padding: 0px;
				margin: 0px;
				display: none;
			}
			
			#widget<?php echo $widget->getGUID();?> h3 {
				font-size: 1em;
			}
			
			#widget<?php echo $widget->getGUID();?> #thewire_large-textarea {
				width: 94%;
			}
		</style>
		<script type="text/javascript">
	
			$(function(){
				$("#widget<?php echo $widget->getGUID();?> .post_to_wire form").submit(function(){
					if($(this).find("textarea").val() != ""){
						$("#widgetcontent<?php echo $widget->getGUID(); ?>").html('<?php echo elgg_view("ajax/loader", array("slashes" => true)); ?>');
						$.post($(this).attr("action"), $(this).serialize(), function(){
							$("#widgetcontent<?php echo $widget->getGUID(); ?>").load("<?php echo $vars["url"]; ?>pg/view/<?php echo $widget->getGUID(); ?>?shell=no&username=<?php echo page_owner_entity()->username; ?>&context=<?php echo get_context(); ?>&callback=true");	
						});
					}
					return false;
				});
			});
			
		</script>
		<?php 
	}
	
	echo $list;
	
	if(empty($error)){
		echo "<div class='widget_more_wrapper'>";
		echo elgg_view("output/url", array("href" => $vars["url"] . "pg/thewire/group/" . $widget->container_guid, "text" => elgg_echo("thewire:moreposts")));
		echo "</div>";
	}
	