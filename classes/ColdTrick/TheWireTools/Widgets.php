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
				
				switch ($widget->owner) {
					case 'friends':
						return elgg_generate_url('collection:object:thewire:friends', [
							'username' => $owner->username,
						]);
						
					case 'all':
						return elgg_generate_url('collection:object:thewire:all');
						
					default:
						if ($owner instanceof \ElggGroup) {
							return elgg_generate_url('collection:object:thewire:group', [
								'guid' => $owner->guid,
							]);
						}
						break;
				}
				break;
			
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
