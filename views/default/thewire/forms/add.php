<?php

	/**
	 * Elgg thewire edit/add page
	 * 
	 * @package ElggTheWire
	 * 
	 */

	global $THEWIRE_TOOLS_ENABLE_GROUP;
	if(isloggedin()){
		$reply = $vars["reply"];
		$wire_user = get_input('wire_username');
		if (!empty($wire_user)) { 
			$msg = '@' . $wire_user . ' '; 
		} else { 
			$msg = ''; 
		}
		
		$widget_context = $vars["widget"];
		if($widget_context !== true){
			$widget_context = false;
		}
		
		$page_owner = page_owner_entity();
		$user = get_loggedin_user();
		
		if($THEWIRE_TOOLS_ENABLE_GROUP){
			if(isset($vars["access_id"]) && !is_null($vars["access_id"])){
				$access_id = (int) $vars["access_id"];
			} else {
				$access_id = get_default_access($user);
				
				// get access array for this user
				$access_options = get_write_access_array($user->getGUID());
				// unset private access, useless on wire posts
				if(isset($access_options[ACCESS_PRIVATE])){
					unset($access_options[ACCESS_PRIVATE]);
				}
				// unset friends access
				if(isset($access_options[ACCESS_FRIENDS])){
					unset($access_options[ACCESS_FRIENDS]);
				}
				
				// check if some groups have disabled the wire
				$options = array(
					"type" => "group",
					"limit" => false,
					"relationship" => "member",
					"relationship_guid" => $user->getGUID(),
					"metadata_name_value_pairs" => array("thewire_enable" => "no")
				);
				
				if($groups = elgg_get_entities_from_relationship($options)){
					foreach($groups as $group){
						if(array_key_exists($group->group_acl, $access_options)){
							unset($access_options[$group->group_acl]);
						}
					}
				}
			}
		} else {
			$access_id = get_default_access();
		}
		
		if($access_id == ACCESS_PRIVATE){
			$access_id = ACCESS_LOGGED_IN;
		}
	
		if (!isset($autocomplete_js_loaded)) {
			$autocomplete_js_loaded = false;
		}
		
		if($page_owner instanceof ElggGroup){
			$group_guid = page_owner();
		} 
	?>
	<div class="post_to_wire">
		<h3><?php echo elgg_echo("thewire:doing"); ?></h3>
	
	<?php 
		if (!$autocomplete_js_loaded) {
			$autocomplete_js_loaded = true;
	?>
		<script type="text/javascript" src="<?php echo $vars["url"]; ?>vendors/jquery/jquery.autocomplete.min.js"></script>
	<?php } ?>
		<script type="text/javascript">
			function textCounter(field, cntfield, maxlimit) {
			    // if too long...trim it!
			    if (field.value.length > maxlimit) {
			        field.value = field.value.substring(0, maxlimit);
			    } else {
			        // otherwise, update 'characters left' counter
			        cntfield.value = maxlimit - field.value.length;
			    }
			}
		
			$(document).ready(function(){
				$('#thewire_large-textarea').bind("keyup keydown", function(event){
					textCounter(this, document.noteForm.remLen1, 140);
				}).autocomplete("<?php echo $vars["url"]; ?>pg/thewire/autocomplete", {
					mustMatch: false,
					multiple: true,
					cacheLength: 1,
					extraParams: {group_guid: "<?php echo $group_guid; ?>"},
					multipleSeparator: " ",
					formatItem: function(row, pos, count, search){
						if(row[0] == "user"){
							return "<img src='" + row[3] + "'> " + row[2];
						} else {
							return "#" + row[1];
						}
						
					},
					formatResult: function(row, pos, count){
						if(row[0] == "user"){
							return "@" + row[1];
						} else {
							return "#" + row[1];
						}					
					}
				}).ajaxSend(function(e, jqxhr, settings) {
					var location = unescape(settings.url); 
					if(location.indexOf("<?php echo $vars["url"]; ?>pg/thewire/autocomplete") === 0){
						location = location.replace("<?php echo $vars["url"]; ?>pg/thewire/autocomplete?q=", "");
						
						if(!(location.substr(0,1) == "#" || location.substr(0,1) == "@")){
							jqxhr.abort();
							$(this).removeClass("ac_loading");
						}
					}				 
				});
			});
		</script>
		
		<form action="<?php echo $vars['url']; ?>action/thewire/add" method="post" name="noteForm">
			<?php
			    $display .= "<textarea name='note' id='thewire_large-textarea'>". $msg . "</textarea>";
				
			    $display .= "<div class='thewire_characters_remaining'>";
				$display .= "<input readonly type='text' name='remLen1' size='3' maxlength='3' value='140' class='thewire_characters_remaining_field'>";
				$display .= elgg_echo("thewire:charleft");
				$display .= "</div>";
				
				// set container guid
				if(!empty($access_options)){
					$access_class = "input-access";
					if($widget_context){
						$access_class .= " thewire_tools_widget_access";
					}
					
					$display .= "<div class='thewire_tools_container_wrapper'>";
					$display .= elgg_view("input/access", array("internalname" => "access_id", "options" => $access_options, "value" => $access_id, "class" => $access_class));
					$display .= "</div>";
				} else {
					$display .= elgg_view("input/hidden", array("internalname" => "access_id", "value" => $access_id));
				}
				$display .= "<div class='clearfloat'></div>";
				
				$display .= elgg_view('input/securitytoken');
				echo $display;
				
				// log reply
				if(!empty($reply)){
					echo elgg_view("input/hidden", array("internalname" => "reply_guid", "value" => $reply->getGUID()));
					echo elgg_view("input/hidden", array("internalname" => "forward_url", "value" => $_SERVER["HTTP_REFERER"]));
					
					if(!empty($reply->conversation)){
						echo elgg_view("input/hidden", array("internalname" => "conversation", "value" => $reply->conversation));
					} else {
						echo elgg_view("input/hidden", array("internalname" => "conversation", "value" => $reply->getGUID()));
					}
				}
			?>
			<input type="hidden" name="method" value="site" />
			<input type="submit" value="<?php echo elgg_echo('send'); ?>" />
		</form>
	</div>
	<?php 
		echo elgg_view('input/urlshortener'); 
	}