<?php
/**
 * Prepend a form before the river
 */

if (!elgg_is_logged_in()) {
	return;
}

if (elgg_get_plugin_setting('extend_activity', 'thewire_tools') !== 'yes') {
	return;
}

echo elgg_view_form('thewire/add');
