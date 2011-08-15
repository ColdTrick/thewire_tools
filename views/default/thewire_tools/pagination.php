<?php 

	$body = "<div class='contentWrapper' id='thewire_tools_pagination'>";
	$body .= "<div onclick='thewire_tools_load();'>" . elgg_echo("more") . " ...</div>";
	$body .= "<img src='" . $vars["url"] . "_graphics/ajax_loader.gif'/>";
	$body .= "</div>";
	
	echo $body;
	
	$current_url_parts = parse_url(current_page_url());

	$params = array(
		"offset" => ($vars["options"]["offset"] + $vars["options"]["limit"]), 
		"limit" => $vars["options"]["limit"], 
		"entities_only" => true
		);
	
	$query = get_input("query", get_input("q"));
		
	if(!empty($query)){
		$params["q"] = $query;
	}
	
	$post_url = $current_url_parts["scheme"] . "://" . $current_url_parts["host"] . $current_url_parts["path"] . "?" . http_build_query($params);
	
	?>
	<script type="text/javascript">
	function thewire_tools_load(){
	
		$("#thewire_tools_pagination").addClass("loading");
	
		$.post("<?php echo $post_url;?>", function(return_data){
			$("#thewire_tools_pagination").before(return_data).remove();
		}); 
	}
	</script>
	