<?php

namespace ColdTrick\TheWireTools\Plugins;

/**
 * Modification for the Widget Manager plugin
 */
class WidgetManager {
	
	/**
	 * Add or remove widgets based on the group tool option
	 *
	 * @param \Elgg\Event $event 'group_tool_widgets', 'widget_manager'
	 *
	 * @return null|array
	 */
	public static function groupToolBasedWidgets(\Elgg\Event $event): ?array {
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggGroup) {
			return null;
		}
		
		$return = $event->getValue();
		if (!is_array($return)) {
			$return = [];
		}
		
		if (!isset($return['enable'])) {
			$return['enable'] = [];
		}
		
		if (!isset($return['disable'])) {
			$return['disable'] = [];
		}
		
		// check different group tools for which we supply widgets
		if ($entity->isToolEnabled('thewire')) {
			$return['enable'][] = 'thewire_groups';
		} else {
			$return['disable'][] = 'thewire_groups';
		}
		
		return $return;
	}
}
