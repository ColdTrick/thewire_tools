<?php

namespace ColdTrick\TheWireTools;

/**
 * Change views
 */
class Views {
	
	/**
	 * Add the add form to thewire group listings
	 *
	 * @param \Elgg\Event $event 'view_vars', 'thewire/list_form'
	 *
	 * @return array|null
	 */
	public static function listAddForm(\Elgg\Event $event): ?array {
		$vars = $event->getValue();
		if (elgg_extract('page', $vars) !== 'group') {
			return null;
		}
		
		$group = elgg_get_page_owner_entity();
		if (!$group instanceof \ElggGroup) {
			return null;
		}
		
		$vars['add_form'] = $group->isMember();
		
		return $vars;
	}
}
