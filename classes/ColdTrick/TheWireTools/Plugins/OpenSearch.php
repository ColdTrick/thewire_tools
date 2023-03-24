<?php

namespace ColdTrick\TheWireTools\Plugins;

/**
 * Additions to the OpenSearch plugin
 */
class OpenSearch {
	
	/**
	 * Export reshare counter
	 *
	 * @param \Elgg\Event $event 'export:counters', 'opensearch'
	 *
	 * @return null|array
	 */
	public static function exportCounter(\Elgg\Event $event): ?array {
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggEntity) {
			return null;
		}
		
		$return = $event->getValue();
		
		$return['thewire_reshare'] = elgg_call(ELGG_IGNORE_ACCESS, function () use ($entity) {
			return elgg_count_entities([
				'relationship' => 'reshare',
				'relationship_guid' => $entity->guid,
				'inverse_relationship' => true,
			]);
		});
		
		return $return;
	}
}
