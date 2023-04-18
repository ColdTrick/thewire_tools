<?php

namespace ColdTrick\TheWireTools\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the social menu
 */
class Social {
	
	/**
	 * Add reshare menu item
	 *
	 * @param \Elgg\Event $event 'register', 'menu:social'
	 *
	 * @return null|MenuItems
	 */
	public static function registerReshare(\Elgg\Event $event): ?MenuItems {
		if (!elgg_is_logged_in()) {
			return null;
		}
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggEntity) {
			return null;
		}
		
		if (!self::canReshareEntity($entity)) {
			return null;
		}
		
		$reshare_guid = $entity->guid;
		$reshare = null;
		
		if ($entity instanceof \ElggWire) {
			$reshares = $entity->getEntitiesFromRelationship([
				'relationship' => 'reshare',
				'limit' => 1,
				'callback' => function($row) {
					return (int) $row->guid;
				},
			]);
			if (!empty($reshares)) {
				// this is a wire post which is a reshare, so link to original object
				$reshare_guid = $reshares[0];
			}
		}
		
		$menu_options = [
			'name' => 'thewire_tools_reshare',
			'icon' => 'share-alt-square',
			'text' => elgg_echo('thewire_tools:reshare'),
			'title' => elgg_echo('thewire_tools:reshare'),
			'href' => elgg_http_add_url_query_elements('ajax/view/thewire_tools/reshare', [
				'reshare_guid' => $reshare_guid,
			]),
			'link_class' => 'elgg-lightbox',
			'is_trusted' => true,
			'priority' => 500,
			'data-colorbox-opts' => json_encode([
				'scrolling' => false,
			]),
		];
		
		if (empty($reshare)) {
			// check is this item was shared on thewire
			$count = $entity->getEntitiesFromRelationship([
				'type' => 'object',
				'subtype' => 'thewire',
				'relationship' => 'reshare',
				'inverse_relationship' => true,
				'count' => true,
			]);
			
			if ($count) {
				// show counter
				$menu_options['badge'] = $count;
				$menu_options['data-badge-link'] = elgg_normalize_url(elgg_http_add_url_query_elements('ajax/view/thewire_tools/reshare_list', [
					'entity_guid' => $reshare_guid,
				]));
				$menu_options['deps'][] = 'thewire_tools/ReshareBadge';
			}
		}
		
		$result = $event->getValue();
		
		$result[] = \ElggMenuItem::factory($menu_options);
		
		return $result;
	}
	
	/**
	 * Check if resharing of this entity is allowed
	 *
	 * @param \ElggEntity $entity the entity to check
	 *
	 * @return bool
	 */
	protected static function canReshareEntity(\ElggEntity $entity) {
		// only allow objects and groups
		if (!$entity instanceof \ElggObject && !$entity instanceof \ElggGroup) {
			return false;
		}
		
		// comments and discussion replies are never allowed
		if ($entity instanceof \ElggComment) {
			return false;
		}
		
		// private content can't be reshared
		if ($entity->access_id === ACCESS_PRIVATE) {
			return false;
		}
		
		// by default allow searchable entities
		$reshare_allowed = $entity->hasCapability('searchable');
		
		// trigger event to allow others to change
		$params = [
			'entity' => $entity,
			'user' => elgg_get_logged_in_user_entity(),
		];
		return (bool) elgg_trigger_event_results('reshare', $entity->getType(), $params, $reshare_allowed);
	}
}
