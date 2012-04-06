<?php

	/**
	 * Elgg thewire: delete note action
	 * 
	 * @package ElggTheWire
	 */

	// Get input data
	$guid = (int) get_input('thewirepost');
		
	// Make sure we actually have permission to edit
	if (($thewire = get_entity($guid)) && elgg_instanceof($thewire, "object","thewire") && $thewire->canEdit()) {

		// Get owning user
		$owner = $thewire->getOwnerEntity();
	
		// Delete it!
		if ($thewire->delete()) {
			// Success message
			system_message(elgg_echo("thewire:deleted"));
		} else {
			register_error(elgg_echo("thewire:notdeleted"));
		}
	}

	forward(REFERER);