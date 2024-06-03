<?php

namespace ColdTrick\TheWireTools;

/**
 * Widget modifications
 */
class Widgets {
	
	/**
	 * Returns the correct widget title url
	 *
	 * @param \Elgg\Event $event 'entity:url', 'object'
	 *
	 * @return null|string
	 */
	public static function widgetTitleURL(\Elgg\Event $event): ?string {
		$widget = $event->getEntityParam();
		if (!$widget instanceof \ElggWidget) {
			return null;
		}
		
		switch ($widget->handler) {
			case 'thewire':
				$owner = $widget->getOwnerEntity();
				if (!$owner instanceof \ElggGroup) {
					return null;
				}
				return elgg_generate_url('collection:object:thewire:group', [
					'guid' => $owner->guid,
				]);
			
			case 'index_thewire':
			case 'thewire_post':
				return elgg_generate_url('collection:object:thewire:all');
				
			case 'thewire_groups':
				return elgg_generate_url('collection:object:thewire:group', [
					'guid' => $widget->owner_guid,
				]);
		}
		
		return null;
	}
}
