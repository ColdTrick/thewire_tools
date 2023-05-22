<?php

namespace ColdTrick\TheWireTools\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the entity menu
 */
class Entity {
	
	/**
	 * Improves menu items
	 *
	 * @param \Elgg\Event $event 'register', 'menu:entity'
	 *
	 * @return null|MenuItems
	 */
	public static function registerImprove(\Elgg\Event $event): ?MenuItems {
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggWire) {
			return null;
		}
		
		/* @var $return MenuItems */
		$return = $event->getValue();
		$current_route = elgg_get_current_route();
		
		// rework items
		if ($return->has('thread')) {
			$on_thread_page = false;
			if (!empty($current_route) && $current_route->getName() === 'collection:object:thewire:thread') {
				$on_thread_page = true;
			}
			
			if (elgg_in_context('thewire_tools_thread') || $on_thread_page) {
				$return->remove('thread');
			} elseif (!($entity->countEntitiesFromRelationship('parent') || $entity->countEntitiesFromRelationship('parent', true))) {
				$return->remove('thread');
			} else {
				/* @var $menu_item \ElggMenuItem */
				$menu_item = $return->get('thread');
				
				$menu_item->rel = $entity->guid;
				$menu_item->setSection('_begin');
				$menu_item->addDeps('thewire_tools/ThreadLoader');
			}
		}
		
		if ($return->has('reply')) {
			if (elgg_in_context('thewire_tools_thread')) {
				$return->remove('reply');
			} else {
				/* @var $menu_item \ElggMenuItem */
				$menu_item = $return->get('reply');
				
				$menu_item->addDeps([
					'thewire_tools/ReplyLoader',
				]);
				$menu_item->{'data-thewire-reply'} = $entity->guid;
				$menu_item->setHref("#thewire-tools-reply-{$entity->guid}");
			}
		}
		
		return $return;
	}
	
	/**
	 * Add feature menu items for a wire post
	 *
	 * @param \Elgg\Event $event 'register', 'menu:entity'
	 *
	 * @return null|MenuItems
	 */
	public static function registerFeature(\Elgg\Event $event): ?MenuItems {
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggWire) {
			return null;
		}
		
		$container = $entity->getContainerEntity();
		if ($container instanceof \ElggGroup) {
			if (!$container->canEdit()) {
				return null;
			}
		} elseif (!elgg_is_admin_logged_in()) {
			return null;
		}
		
		$toggle_action = elgg_generate_action_url('thewire_tools/toggle_feature', ['guid' => $entity->guid]);
		
		/* @var $result MenuItems */
		$return = $event->getValue();
		$return[] = \ElggMenuItem::factory([
			'name' => 'thewire-tools-feature',
			'icon' => 'arrow-up',
			'text' => elgg_echo('thewire_tools:feature'),
			'href' => $toggle_action,
			'item_class' => $entity->featured ? 'hidden' : '',
			'data-toggle' => 'thewire-tools-unfeature',
			'priority' => 200,
		]);
		
		$return[] = \ElggMenuItem::factory([
			'name' => 'thewire-tools-unfeature',
			'icon' => 'arrow-down',
			'text' => elgg_echo('thewire_tools:unfeature'),
			'href' => $toggle_action,
			'item_class' => $entity->featured ? '' : 'hidden',
			'data-toggle' => 'thewire-tools-feature',
			'priority' => 201,
		]);
		
		return $return;
	}
}
