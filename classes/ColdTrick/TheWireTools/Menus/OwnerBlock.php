<?php

namespace ColdTrick\TheWireTools\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the owner_block menu
 */
class OwnerBlock {
	
	/**
	 * Optionally extend the group owner block with a link to the wire posts of the group
	 *
	 * @param \Elgg\Event $event 'register', 'menu:owner_block'
	 *
	 * @return null|MenuItems
	 */
	public static function registerGroup(\Elgg\Event $event): ?MenuItems {
		$group = $event->getEntityParam();
		if (!$group instanceof \ElggGroup || !$group->isToolEnabled('thewire')) {
			return null;
		}
		
		/* @var $return MenuItems */
		$return = $event->getValue();
		
		$return[] = \ElggMenuItem::factory([
			'name' => 'thewire',
			'text' => elgg_echo('collection:object:thewire:group'),
			'href' => elgg_generate_url('collection:object:thewire:group', [
				'guid' => $group->guid,
			]),
		]);
		
		return $return;
	}
}
