<?php

namespace ColdTrick\TheWireTools;

class Widgets {
	
	/**
	 * Add or remove widgets based on the group tool option
	 *
	 * @param \Elgg\Hook $hook 'group_tool_widgets', 'widget_manager'
	 *
	 * @return array
	 */
	public static function groupToolBasedWidgets(\Elgg\Hook $hook) {
	
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \ElggGroup) {
			return;
		}
		
		$return = $hook->getValue();
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
	
	/**
	 * Returns the correct widget title url
	 *
	 * @param \Elgg\Hook $hook 'entity:url', 'object'
	 *
	 * @return void|string the url for the widget
	 */
	public static function widgetTitleURL(\Elgg\Hook $hook) {
	
		$widget = $hook->getEntityParam();
		if (!$widget instanceof \ElggWidget) {
			return;
		}
		
		switch ($widget->handler) {
			case 'thewire':
				return elgg_generate_url('collection:object:thewire:owner', [
					'username' => $widget->getOwnerEntity()->username,
				]);
			
			case 'index_thewire':
			case 'thewire_post':
				return elgg_generate_url('collection:object:thewire:all');
				
			case 'thewire_groups':
				return elgg_generate_url('collection:object:thewire:group', [
					'guid' => $widget->owner_guid,
				]);
		}
	}
	
	/**
	 * Unregisters a widget handler in case of group
	 *
	 * @param \Elgg\Hook $hook 'handlers', 'widgets'
	 *
	 * @return \Elgg\WidgetDefinition[]
	 */
	public static function registerHandlers(\Elgg\Hook $hook) {
		
		$container = $hook->getParam('container');
		if (!$container instanceof \ElggGroup) {
			return;
		}
		
		if ($container->isToolEnabled('thewire')) {
			return;
		}
		
		$return = $hook->getValue();
		
		/* @var $widget \Elgg\WidgetDefinition */
		foreach ($return as $index => $widget) {
			if ($widget->id !== 'thewire_groups') {
				continue;
			}
			unset($return[$index]);
			break;
		}
		
		return $return;
	}
}
