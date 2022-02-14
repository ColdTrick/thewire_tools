<?php

namespace ColdTrick\TheWireTools;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritdoc}
	 */
	public function init() {
		
		if ($this->plugin()->getSetting('enable_group') === 'yes') {
			// add widget (for Widget Manager only)
			elgg_register_widget_type([
				'id' => 'thewire_groups',
				'name' => elgg_echo('widgets:thewire_groups:title'),
				'description' => elgg_echo('widgets:thewire_groups:description'),
				'context' => ['groups'],
				'multiple' => true,
			]);
			
			// add group tool option
			$this->elgg()->group_tools->register('thewire', [
				'label' => elgg_echo('thewire_tools:groups:tool_option'),
				'default_on' => true,
			]);
		}
		
		$this->registerHooks();
	}
	
	/**
	 * Register plugin hook handlers
	 *
	 * @return void
	 */
	protected function registerHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('entity:url', 'object', __NAMESPACE__ . '\Widgets::widgetTitleURL');
		$hooks->registerHandler('export:counters', 'elasticsearch', __NAMESPACE__ . '\Elasticsearch::exportCounter');
		$hooks->registerHandler('group_tool_widgets', 'widget_manager', __NAMESPACE__ . '\Widgets::groupToolBasedWidgets');
		$hooks->registerHandler('handlers', 'widgets', __NAMESPACE__ . '\Widgets::registerHandlers');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\Menus::entityRegisterImprove', 501);
		$hooks->registerHandler('register', 'menu:social', __NAMESPACE__ . '\Menus::entityRegisterReshare');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\Menus::entityRegisterFeature');
		$hooks->registerHandler('register', 'menu:owner_block', __NAMESPACE__ . '\Menus::ownerBlockRegister');
		$hooks->registerHandler('register', 'menu:page', __NAMESPACE__ . '\Menus::pageRegister');
		if (elgg_is_active_plugin('entity_tools')) {
			$hooks->registerHandler('supported_types', 'entity_tools', __NAMESPACE__ . '\Migrate::registerClass');
		}
	}
}
