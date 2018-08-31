<?php

namespace ColdTrick\TheWireTools;

class Elasticsearch {
	
	/**
	 * Export reshare counter
	 *
	 * @param \Elgg\Hook $hook 'export:counters', 'elasticsearch'
	 *
	 * @return void|array
	 */
	public static function exportCounter(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \ElggEntity) {
			return;
		}
		
		$return = $hook->getValue();
		
		$return['thewire_reshare'] = elgg_call(ELGG_IGNORE_ACCESS, function () use ($entity) {
			return elgg_get_entities([
				'relationship' => 'reshare',
				'relationship_guid' => $entity->guid,
				'inverse_relationship' => true,
				'count' => true,
			]);
		});
		
		return $return;
	}
}
