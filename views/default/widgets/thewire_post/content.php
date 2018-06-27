<?php

if (!elgg_is_logged_in()) {
	echo elgg_echo('thewire_tools:login_required');
	return;
}

echo elgg_view_form('thewire/add');
