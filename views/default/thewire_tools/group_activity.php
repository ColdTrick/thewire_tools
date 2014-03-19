<?php
/**
 * This view prepends the content layout to check if we need to add a wire add form to the group acivity listing
 */

if (elgg_is_logged_in() && elgg_in_context("groups")) {
	$page_owner = elgg_get_page_owner_entity();
	
	$page = current_page_url();
	$page = str_ireplace(elgg_get_site_url(), "", $page);
	
	if (!empty($page_owner) && elgg_instanceof($page_owner, "group")) {
		// check if we're on the activity page
		if (strpos($page, "groups/activity/" . $page_owner->getGUID()) === 0) {
			// check the plugin setting
			if (elgg_get_plugin_setting("extend_activity", "thewire_tools") == "yes") {
				
				echo elgg_view_form("thewire/add");
			}
		}
	}
}