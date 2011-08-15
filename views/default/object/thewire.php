<?php

	/**
	 * Elgg thewire note view
	 * 
	 * @package ElggTheWire
	 *
	 * @question - do we want users to be able to edit thewire?
	 * 
	 * @uses $vars['entity'] Optionally, the note to view
	 */

	if (isset($vars['entity'])) {
    		
    	$user = $vars['entity']->getOwnerEntity();
		$user_url = $vars['url'] . "pg/thewire/owner/" . $user->username;
		$user_link = elgg_view('output/url', array('href' => $user_url, 'text' => $user->name));
    	
    	//if the note is a reply, we need some more info
		$note_url = '';
		$note_owner = elgg_echo("thewire:notedeleted");
		
		$context = get_context();
    		
?>
<div class="thewire-singlepage">
	<div class="thewire-post">
			    
	    <!-- the actual shout -->
		<div class="note_body">

		    <div class="thewire_icon">
		    	<?php 
		    		$size = "small";
		    		if($context == "thewire_tools_conversation"){
		    			$size = "tiny";	
		    		}
		    		
		    		echo elgg_view("profile/icon",array('entity' => $vars['entity']->getOwnerEntity(), 'size' => $size)); 
		    	?>
		    </div>

			<div class="thewire_options">
				<?php 
					if (isloggedin()) {
						echo elgg_view("output/url", array("href" => $vars["url"] . "pg/thewire/reply/" . $vars['entity']->getGUID(), "class" => "reply", "text" => elgg_echo('thewire:reply')));
					}
				?>
	    
	    		<div class="clearfloat"></div>
	    		
				<?php
					
					// if the user looking at thewire post can edit, show the delete link
					if ($vars['entity']->canEdit()) {
						echo "<div class='delete_note'>" . elgg_view("output/confirmlink", array(
															'href' => $vars['url'] . "action/thewire/delete?thewirepost=" . $vars['entity']->getGUID(),
															'text' => elgg_echo('delete'),
															'confirm' => elgg_echo('deleteconfirm'),
														)) . "</div>";
			
					} //end of can edit if statement
		?>
			</div>
	    
			<div class="note_text">
				<?php
					echo "<b>" . $user_link . ": </b>";
				    
				    $desc = $vars['entity']->description;
				    
				    // add clickable usernames
				    $desc = preg_replace('/\@([A-Za-z0-9\_\.\-]+)/i','<a title="$1" href="' . $vars['url'] . 'pg/thewire/owner/$1">@$1</a>',$desc);
				    
				    // add clickable hashtags
				    $desc = preg_replace('/\#([A-Za-z0-9\_\.\-]+)/i','<a title="$1" href="' . $vars['url'] . 'pg/thewire/search?q=%23$1">#$1</a>',$desc);
				    
				    echo parse_urls($desc);
				?>
			</div>
			<?php
			if($vars["entity"]->conversation && ($context != "thewire_tools_conversation")){ 
				echo "<div class='thewire_conversation'><a href='javascript:void(0);' onclick='thewire_tools_show_conversation(" . $vars["entity"]->getGUID() . ");'>" . elgg_echo("thewire_tools:show_conversation") . "</a><div id='thewire_tools_conversation_" . $vars["entity"]->getGUID() . "' class='thewire_tools_conversation'></div></div>";				
			}
			?>
			<div class="clearfloat"></div>
			
		</div>
		
		<div class="note_date">
		<?php
			// time posted
			echo elgg_echo("thewire:wired") . " " . sprintf(elgg_echo("thewire:strapline"), elgg_view_friendly_time($vars['entity']->time_created));

			// check if need to show group
			if(($vars["entity"]->owner_guid != $vars["entity"]->container_guid) && (page_owner() != $vars["entity"]->container_guid)){
				echo " ";
				$group = get_entity($vars["entity"]->container_guid);
				$group_link = elgg_view("output/url", array("href" => $vars["url"] . "pg/thewire/group/" . $group->getGUID(), "text" => $group->name, "class" => "thewire_tools_object_link"));
				echo sprintf(elgg_echo("thewire_tools:object:in_group"), $group_link);
			}
			
			// method used
			$method = $vars['entity']->method;
			if($method !== "site"){
				// only show if not site
				echo ' ';
				echo sprintf(elgg_echo('thewire:via_method'), elgg_echo($method));
				echo '.';
			}
		?>
		</div>
	</div>
</div>
<?php 
	} 
