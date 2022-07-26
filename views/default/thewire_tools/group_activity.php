<?php
/**
 * This view prepends the content layout to check if we need to add a wire add form to the group acivity listing
 */

if (!elgg_is_logged_in() || !elgg_in_context('groups')) {
	return;
}

$group = elgg_get_page_owner_entity();
if (!$group instanceof ElggGroup) {
	return;
}

if (!$group->isToolEnabled('thewire')) {
	return;
}

if (!$group->canEdit() && !$group->isMember()) {
	return;
}

// check if we're on the activity page
if (elgg_get_current_route_name() !== 'collection:river:group') {
	return;
}

// check the plugin setting
if (elgg_get_plugin_setting('extend_activity', 'thewire_tools') == 'yes') {
	echo elgg_view_form('thewire/add');
}
