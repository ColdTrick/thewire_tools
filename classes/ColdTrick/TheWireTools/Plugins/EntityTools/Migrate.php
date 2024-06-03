<?php

namespace ColdTrick\TheWireTools\Plugins\EntityTools;

use Elgg\Database\QueryBuilder;

/**
 * Adjusted version of TheWire migration to support groups
 */
class Migrate extends \ColdTrick\EntityTools\Migrate\TheWire {
	
	/**
	 * {@inheritdoc}
	 */
	public function canChangeContainer(): bool {
		$page_owner_entity = elgg_get_page_owner_entity();
		if ($page_owner_entity) {
			// viewing a listing
			return (bool) ($page_owner_entity instanceof \ElggGroup);
		}
		
		$object = $this->getObject();
		
		// no listing so just checking the entity
		if (!$object->getContainerEntity() instanceof \ElggGroup) {
			return false;
		}
		
		// check if object is conversation starter
		return (bool) ($object->guid == $object->wire_thread);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function changeContainer($new_container_guid): void {
		// do all the default stuff
		parent::changeContainer($new_container_guid);
		
		// move all items in thread to the new container
		if ($this->getObject()->guid == $this->getObject()->wire_thread) {
			$this->moveThreadItems($new_container_guid);
		}
	}
		
	/**
	 * Move all the posts in the thread to the new container_guid
	 *
	 * @param int $new_container_guid the new container_guid
	 *
	 * @return void
	 */
	protected function moveThreadItems(int $new_container_guid): void {
		// ignore access for this part
		elgg_call(ELGG_IGNORE_ACCESS, function() use ($new_container_guid) {
			$object = $this->getObject();
			
			$batch = elgg_get_entities([
				'type' => 'object',
				'subtype' => 'thewire',
				'limit' => false,
				'batch' => true,
				'metadata_name_value_pairs' => [
					'name' => 'wire_thread',
					'value' => $object->guid,
				],
				'wheres' => [
					function(QueryBuilder $qb, $main_alias) use ($object) {
						return $qb->compare("{$main_alias}.guid", '!=', $object->guid, ELGG_VALUE_GUID);
					},
				]
			]);
			
			/* @var $post \ElggWire */
			foreach ($batch as $post) {
				$migrate = new Migrate($post);
				$migrate->changeContainer($new_container_guid);
				
				$post->save();
			}
		});
	}
}
