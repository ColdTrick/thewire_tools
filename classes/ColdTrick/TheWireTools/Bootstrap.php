<?php

namespace ColdTrick\TheWireTools;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritdoc}
	 */
	public function init() {

		if (elgg_get_plugin_setting('enable_group', 'thewire_tools') === 'yes') {
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
	
	protected function registerViews() {
		
		elgg_extend_view('core/river/filter', 'thewire_tools/activity_post', 400);
		elgg_extend_view('css/elgg', 'css/thewire_tools.css');
		elgg_extend_view('js/elgg', 'js/thewire_tools.js');
		elgg_extend_view('notifications/subscriptions/personal', 'thewire_tools/notifications/settings');
		elgg_extend_view('page/layouts/elements/filter', 'thewire_tools/group_activity', 400);
		elgg_extend_view('thewire/sidebar', 'thewire_tools/extends/thewire/sidebar', 400);
		
		elgg_register_ajax_view('thewire_tools/reshare');
		elgg_register_ajax_view('thewire_tools/reshare_list');
		elgg_register_ajax_view('thewire_tools/thread');
	}
	
	protected function registerEvents() {
		elgg_register_event_handler('create', 'object', '\ColdTrick\TheWireTools\Notifications::triggerMentionNotificationEvent');
	}
	
	protected function registerHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('cron', 'daily', __NAMESPACE__ . '\Cron::daily');
		$hooks->registerHandler('entity:url', 'object', '\ColdTrick\TheWireTools\Widgets::widgetTitleURL');
		$hooks->registerHandler('export:counters', 'elasticsearch', __NAMESPACE__ . '\Elasticsearch::exportCounter');
		$hooks->registerHandler('group_tool_widgets', 'widget_manager', '\ColdTrick\TheWireTools\Widgets::groupToolBasedWidgets');
		$hooks->registerHandler('register', 'menu:entity', '\ColdTrick\TheWireTools\Menus::entityRegisterImprove');
		$hooks->registerHandler('register', 'menu:social', '\ColdTrick\TheWireTools\Menus::entityRegisterReshare');
		$hooks->registerHandler('register', 'menu:entity', '\ColdTrick\TheWireTools\Menus::entityRegisterFeature');
		$hooks->registerHandler('register', 'menu:river', '\ColdTrick\TheWireTools\Menus::riverRegisterReply');
		$hooks->registerHandler('register', 'menu:owner_block', '\ColdTrick\TheWireTools\Menus::ownerBlockRegister');
		$hooks->registerHandler('register', 'menu:page', '\ColdTrick\TheWireTools\Menus::pageRegister');
		$hooks->registerHandler('action', 'notificationsettings/save', '\ColdTrick\TheWireTools\Notifications::saveUserNotificationsSettings');
		$hooks->registerHandler('handlers', 'widgets', '\ColdTrick\TheWireTools\Widgets::registerHandlers');
		$hooks->registerHandler('search:format', 'entity', '\ColdTrick\TheWireTools\Search::formatEntity');
		$hooks->registerHandler('supported_types', 'entity_tools', '\ColdTrick\TheWireTools\Migrate::registerClass');
		
	}
}
