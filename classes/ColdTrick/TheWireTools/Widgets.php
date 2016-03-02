<?php

namespace ColdTrick\TheWireTools;

class Widgets {
	
	
	/**
	 * Add or remove widgets based on the group tool option
	 *
	 * @param string $hook_name   'group_tool_widgets'
	 * @param string $entity_type 'widget_manager'
	 * @param array  $return      current enable/disable widget handlers
	 * @param array  $params      supplied params
	 *
	 * @return array
	 */
	public static function groupToolBasedWidgets($hook_name, $entity_type, $return, $params) {
	
		$entity = elgg_extract('entity', $params);

		if (!elgg_instanceof($entity, 'group')) {
			return;
		}
		
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
		if ($entity->thewire_enable == 'yes') {
			$return['enable'][] = 'thewire_groups';
		} else {
			$return['disable'][] = 'thewire_groups';
		}
	
		return $return;
	}
	
	/**
	 * returns the correct widget title
	 *
	 * @param string $hook_name   'widget_url'
	 * @param string $entity_type 'widget_manager'
	 * @param string $return      the current widget url
	 * @param array  $params      supplied params
	 *
	 * @return string the url for the widget
	 */
	public static function widgetTitleURL($hook_name, $entity_type, $return, $params) {
	
		$widget = elgg_extract('entity', $params);
		if (!elgg_instanceof($widget, 'object', 'widget')) {
			return;
		}
		
		switch ($widget->handler) {
			case 'thewire':
				$return = "thewire/owner/{$widget->getOwnerEntity()->username}";
				break;
			case 'index_thewire':
			case 'thewire_post':
				$return = 'thewire/all';
				break;
			case 'thewire_groups':
				$return = "thewire/group/{$widget->getOwnerGUID()}";
				break;
		}
	
		return $return;
	}
}