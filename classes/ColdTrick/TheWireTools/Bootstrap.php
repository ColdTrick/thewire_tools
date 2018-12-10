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
		
		$this->registerViews();
		$this->registerEvents();
		$this->registerHooks();
	}
	
	/**
	 * Register view extensions / ajax views
	 *
	 * @return void
	 */
	protected function registerViews() {
		
		elgg_extend_view('river/filter', 'thewire_tools/activity_post', 400);
		elgg_extend_view('css/elgg', 'css/thewire_tools.css');
		elgg_extend_view('js/elgg', 'js/thewire_tools.js');
		elgg_extend_view('notifications/settings/other', 'thewire_tools/notifications/settings');
		elgg_extend_view('page/layouts/elements/filter', 'thewire_tools/group_activity', 400);
		elgg_extend_view('thewire/sidebar', 'thewire_tools/extends/thewire/sidebar', 400);
		
		elgg_register_ajax_view('thewire_tools/reshare');
		elgg_register_ajax_view('thewire_tools/reshare_list');
		elgg_register_ajax_view('thewire_tools/thread');
	}
	
	/**
	 * Register event listeners
	 *
	 * @return void
	 */
	protected function registerEvents() {
		$events = $this->elgg()->events;
		
		$events->registerHandler('create', 'object', __NAMESPACE__ . '\Notifications::triggerMentionNotificationEvent');
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
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\Menus::entityRegisterImprove', 501);
		$hooks->registerHandler('register', 'menu:social', __NAMESPACE__ . '\Menus::entityRegisterReshare');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\Menus::entityRegisterFeature');
		$hooks->registerHandler('register', 'menu:river', __NAMESPACE__ . '\Menus::riverRegisterReply');
		$hooks->registerHandler('register', 'menu:owner_block', __NAMESPACE__ . '\Menus::ownerBlockRegister');
		$hooks->registerHandler('register', 'menu:page', __NAMESPACE__ . '\Menus::pageRegister');
		$hooks->registerHandler('action', 'notifications/settings', __NAMESPACE__ . '\Notifications::saveUserNotificationsSettings');
		$hooks->registerHandler('handlers', 'widgets', __NAMESPACE__ . '\Widgets::registerHandlers');
		$hooks->registerHandler('supported_types', 'entity_tools', __NAMESPACE__ . '\Migrate::registerClass');
		
	}
}
