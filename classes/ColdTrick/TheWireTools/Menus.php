<?php

namespace ColdTrick\TheWireTools;

use Elgg\Menu\MenuItems;

class Menus {
	
	/**
	 * Add reshare menu items to the entity menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:social'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function entityRegisterReshare(\Elgg\Hook $hook) {
		
		if (!elgg_is_logged_in()) {
			return;
		}
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \ElggEntity) {
			return;
		}
		
		if (!self::canReshareEntity($entity)) {
			return;
		}
		
		elgg_load_js('elgg.thewire');
		
		$reshare_guid = $entity->guid;
		$reshare = null;
		
		if ($entity instanceof \ElggWire) {
			$reshare = $entity->getEntitiesFromRelationship([
				'relationship' => 'reshare',
				'limit' => 1,
				'callback' => function($row) {
					return (int) $row->guid;
				},
			]);
			if ($reshare) {
				// this is a wire post which is a reshare, so link to original object
				$reshare_guid = $reshare[0];
			}
		}
		
		$menu_options = [
			'name' => 'thewire_tools_reshare',
			'icon' => 'share',
			'text' => elgg_echo('thewire_tools:reshare'),
			'href' => 'ajax/view/thewire_tools/reshare?reshare_guid=' . $reshare_guid,
			'link_class' => 'elgg-lightbox',
			'is_trusted' => true,
			'priority' => 500,
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
		
		$result = $hook->getValue();
		
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
		
		if (!$entity instanceof \ElggEntity) {
			return false;
		}
		
		// only allow objects and groups
		if (!$entity instanceof \ElggObject && !$entity instanceof \ElggGroup) {
			return false;
		}
		
		// comments and discussion replies are never allowed
		if ($entity instanceof \ElggComment) {
			return false;
		}
		
		// by default allow searchable entities
		$reshare_allowed = false;
		if ($entity instanceof \ElggGroup) {
			$reshare_allowed = true;
		} else {
			$searchable_entities = get_registered_entity_types($entity->getType());
			if (!empty($searchable_entities)) {
				$reshare_allowed = in_array($entity->getSubtype(), $searchable_entities);
			}
		}
		
		// trigger hook to allow others to change
		$params = [
			'entity' => $entity,
			'user' => elgg_get_logged_in_user_entity(),
		];
		return (bool) elgg_trigger_plugin_hook('reshare', $entity->getType(), $params, $reshare_allowed);
	}
	
	/**
	 * Optionally extend the group owner block with a link to the wire posts of the group
	 *
	 * @param string         $hook_name   'register'
	 * @param string         $entity_type 'menu:owner_block'
	 * @param ElggMenuItem[] $return      all the current menu items
	 * @param array          $params      supplied params
	 *
	 * @return ElggMenuItem[]
	 */
	public static function ownerBlockRegister($hook_name, $entity_type, $return, $params) {
		$group = elgg_extract('entity', $params);
		if (!$group instanceof \ElggGroup) {
			return;
		}
		
		if (elgg_get_plugin_setting('enable_group', 'thewire_tools') !== 'yes') {
			return;
		}
	
		if (!$group->isToolEnabled('thewire')) {
			return;
		}
	
		if (!$group->canEdit() && !$group->isMember()) {
			return;
		}

		$return[] = new \ElggMenuItem('thewire', elgg_echo('thewire_tools:group:title'), "thewire/group/{$group->getGUID()}");
	
		return $return;
	}
	
	/**
	 * Improves entity menu items for thewire objects
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:entity'
	 *
	 * @return MenuItems
	 */
	public static function entityRegisterImprove(\Elgg\Hook $hook) {
	
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \ElggWire) {
			return;
		}
		
		/* @var $return MenuItems */
		$return = $hook->getValue();
		$current_route = _elgg_services()->request->getRoute();
		
		// remove items
		$return->remove('previous');
		
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
			}
		}
		
		if ($return->has('reply')) {
			if (elgg_in_context('thewire_tools_thread')) {
				$return->remove('reply');
			} else {
				/* @var $menu_item \ElggMenuItem */
				$menu_item = $return->get('reply');
					
				$menu_item->setHref("#thewire-tools-reply-{$entity->guid}");
				$menu_item->rel = 'toggle';
			}
		}
		
		return $return;
	}
		
	/**
	 * Add feature menu items to the entity menu of a wire post
	 *
	 * @param string          $hook        the name of the hook
	 * @param string          $type        the type of the hook
	 * @param \ElggMenuItem[] $returnvalue current return value
	 * @param array           $params      supplied params
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function entityRegisterFeature($hook, $type, $returnvalue, $params) {
		
		$entity = elgg_extract('entity', $params);
		if (!$entity instanceof \ElggWire) {
			return;
		}
		
		$container = $entity->getContainerEntity();
		if ($container instanceof \ElggGroup) {
			if (!$container->canEdit()) {
				return;
			}
		} elseif (!elgg_is_admin_logged_in()) {
			return;
		}
		
		$toggle_action = elgg_generate_action_url('thewire_tools/toggle_feature', ['guid' => $entity->guid]);
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'thewire-tools-feature',
			'text' => elgg_echo('thewire_tools:feature'),
			'icon' => 'arrow-up',
			'href' => $toggle_action,
			'item_class' => $entity->featured ? 'hidden' : '',
			'data-toggle' => 'thewire-tools-unfeature',
			'priority' => 200,
		]);
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'thewire-tools-unfeature',
			'text' => elgg_echo('thewire_tools:unfeature'),
			'icon' => 'arrow-down',
			'href' => $toggle_action,
			'item_class' => $entity->featured ? '' : 'hidden',
			'data-toggle' => 'thewire-tools-feature',
			'priority' => 201,
		]);
		
		return $returnvalue;
	}
	
	/**
	 * Add menu items to the page menu on thewire pages
	 *
	 * @param string          $hook        the name of the hook
	 * @param string          $type        the type of the hook
	 * @param \ElggMenuItem[] $returnvalue current return value
	 * @param array           $params      supplied params
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function pageRegister($hook, $type, $returnvalue, $params) {
		
		if (!elgg_in_context('thewire')) {
			return;
		}
		
		$page_owner = elgg_get_page_owner_entity();
		if ($page_owner instanceof \ElggGroup) {
			return;
		}
		
		$user = elgg_get_logged_in_user_entity();
		if (!empty($user)) {
			$returnvalue[] = \ElggMenuItem::factory([
				'name' => 'mentions',
				'href' => "thewire/search/@{$user->username}",
				'text' => elgg_echo('thewire_tools:menu:mentions'),
			]);
		}
		
		$returnvalue[] = \ElggMenuItem::factory([
			'name' => 'search',
			'href' => 'thewire/search',
			'text' => elgg_echo('search'),
		]);
		
		return $returnvalue;
	}
}
