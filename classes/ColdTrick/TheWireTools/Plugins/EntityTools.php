<?php

namespace ColdTrick\TheWireTools\Plugins;

use ColdTrick\TheWireTools\Plugins\EntityTools\Migrate;

/**
 * Modifications when the entity_tools plugin is enabled
 */
class EntityTools {
	
	/**
	 * Support migrating wire posts
	 *
	 * @param \Elgg\Event $event 'supported_types', 'entity_tools'
	 *
	 * @return array
	 */
	public static function registerTheWire(\Elgg\Event $event): array {
		$result = $event->getValue();
		
		$result['thewire'] = Migrate::class;
		
		return $result;
	}
}
