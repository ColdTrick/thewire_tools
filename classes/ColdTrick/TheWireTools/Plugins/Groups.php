<?php

namespace ColdTrick\TheWireTools\Plugins;

use Elgg\Collections\Collection;
use Elgg\Groups\Tool;
use Elgg\WidgetDefinition;

/**
 * Modifications when the Groups plugin is active
 */
class Groups {
	
	/**
	 * Register thewire group tool if enabled in the plugin settings
	 *
	 * @param \Elgg\Event $event 'tool_options', 'group'
	 *
	 * @return null|Collection
	 */
	public static function registerGroupTool(\Elgg\Event $event): ?Collection {
		if (elgg_get_plugin_setting('enable_group', 'thewire_tools') !== 'yes') {
			return null;
		}
		
		$result = $event->getValue();
		
		$result[] = new Tool('thewire', [
			'default_on' => true,
		]);
		
		return $result;
	}
	
	/**
	 * Register a group widget if enabled in the plugin settings and enabled by the group
	 *
	 * @param \Elgg\Event $event 'handlers', 'widgets'
	 *
	 * @return array|null
	 */
	public static function registerWidget(\Elgg\Event $event): ?array {
		if (elgg_get_plugin_setting('enable_group', 'thewire_tools') !== 'yes' || !elgg_is_active_plugin('groups')) {
			return null;
		}
		
		$container = $event->getParam('container');
		if ($container instanceof \ElggGroup && !$container->isToolEnabled('thewire')) {
			return null;
		}
		
		$result = $event->getValue();
		
		$result[] = WidgetDefinition::factory([
			'id' => 'thewire_groups',
			'context' => ['groups'],
			'multiple' => true,
		]);
		
		return $result;
	}
}
