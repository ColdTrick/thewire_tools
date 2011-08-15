<?php ?>
function thewire_tools_show_conversation(guid){
	if(guid){
		$conversation = $("#thewire_tools_conversation_" + guid);
	
		if($conversation.html() == ""){
	
	
			$.post("<?php echo $CONFIG->wwwroot; ?>pg/thewire/conversation/" + guid, function(result){
				if(result){
					$conversation.html(result);
				}
				
			});
		}
		
		$conversation.toggle();
	}		
}
