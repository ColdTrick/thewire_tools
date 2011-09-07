<?php 

	if(isloggedin()){
		echo elgg_view("thewire/forms/add", array("widget" => true));
	} else {
		echo elgg_view("page_elements/contentwrapper", array("body" => elgg_echo("thewire_tool:login_required")));
	}
	

?>
<style type="text/css">
	#widget_table #thewire_large-textarea {
		width: 100%;
		padding: 0px;
	}

</style>