<?php

namespace ColdTrick\TheWireTools;

class Search {
	
	/**
	 * Format wirepost entity in search results
	 *
	 * @elgg_plugin_hook search:format entity
	 *
	 * @param \Elgg\Hook $hook Hook
	 *
	 * @return ElggEntity
	 */
	public static function formatEntity(\Elgg\Hook $hook) {

		$entity = $hook->getValue();

		if (!$entity instanceof \ElggWire) {
			return;
		}
		
		if (empty($entity->getVolatileData('search_matched_title'))) {
			return;
		}
		
		$title = elgg_echo('item:object:thewire');
				
		$entity->setVolatileData('search_matched_title', $title);
		
		return $entity;
	}
}
