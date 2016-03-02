<?php
/**
 * Start file for the plugin, is loaded when all active plugins are loaded
 *
 * @package thewire_tools
 */

require_once(dirname(__FILE__) . '/lib/functions.php');

// register default Elgg events
elgg_register_event_handler('init', 'system', 'thewire_tools_init');
elgg_register_event_handler('pagesetup', 'system', 'thewire_tools_pagesetup');

/**
 * This function is called during the 'init' event
 *
 * @return void
 */
function thewire_tools_init() {
	
	elgg_extend_view('js/elgg', 'js/thewire_tools.js');
	elgg_extend_view('css/elgg', 'css/thewire_tools.css');
		
	if (thewire_tools_groups_enabled()) {
		// add widget (for Widget Manager only)
		elgg_register_widget_type('thewire_groups', elgg_echo('widgets:thewire_groups:title'), elgg_echo('widgets:thewire_groups:description'), ['groups'], true);
		
		// add group tool option
		add_group_tool_option('thewire', elgg_echo('thewire_tools:groups:tool_option'), true);
	}
	
	// adds wire post form to the wire widget
	elgg_extend_view('core/river/filter', 'thewire_tools/activity_post', 400);
	elgg_extend_view('page/layouts/elements/filter', 'thewire_tools/group_activity', 400);
	
	// settings
	elgg_extend_view('notifications/subscriptions/personal', 'thewire_tools/notifications/settings');
	
	// register ajax view
	elgg_register_ajax_view('thewire_tools/reshare');
	elgg_register_ajax_view('thewire_tools/reshare_list');
	elgg_register_ajax_view('thewire_tools/thread');
	
	// add some extra widgets (for Widget Manager only)
	elgg_register_widget_type('index_thewire', elgg_echo('widgets:index_thewire:title'), elgg_echo('widgets:index_thewire:description'), ['index'], true);
	elgg_register_widget_type('thewire_post', elgg_echo('widgets:thewire_post:title'), elgg_echo('widgets:thewire_post:description'), ['index', 'dashboard'], false);
	
	// register events
	elgg_register_event_handler('create', 'object', '\ColdTrick\TheWireTools\Notifications::triggerMentionNotificationEvent');
	
	// register hooks
	elgg_register_plugin_hook_handler('route', 'thewire', '\ColdTrick\TheWireTools\Router::thewire');
	elgg_register_plugin_hook_handler('access:collections:write', 'all', '\ColdTrick\TheWireTools\Access::collectionsWrite', 999);
	
	elgg_register_plugin_hook_handler('entity:url', 'object', '\ColdTrick\TheWireTools\Widgets::widgetTitleURL');
	elgg_register_plugin_hook_handler('group_tool_widgets', 'widget_manager', '\ColdTrick\TheWireTools\Widgets::groupToolBasedWidgets');
	
	elgg_register_plugin_hook_handler('register', 'menu:entity', '\ColdTrick\TheWireTools\Menus::entityRegisterImprove');
	elgg_register_plugin_hook_handler('register', 'menu:entity', '\ColdTrick\TheWireTools\Menus::entityRegisterReshare');
	elgg_register_plugin_hook_handler('register', 'menu:river', '\ColdTrick\TheWireTools\Menus::riverRegisterReply');
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', '\ColdTrick\TheWireTools\Menus::ownerBlockRegister');
	
	elgg_register_plugin_hook_handler('action', 'notificationsettings/save', '\ColdTrick\TheWireTools\Notifications::saveUserNotificationsSettings');
		
	// overrule default save action
	elgg_unregister_action('thewire/add');
	elgg_register_action('thewire/add', dirname(__FILE__) . '/actions/thewire/add.php');
}

/**
 * This function is called during the "pagesetup" event
 *
 * @return void
 */
function thewire_tools_pagesetup() {
	$page_owner = elgg_get_page_owner_entity();
	
	if (elgg_instanceof($page_owner, 'group')) {
		// cleanup group widget
		if ($page_owner->thewire_enable == 'no') {
			elgg_unregister_widget_type('thewire_groups');
		}
	} else {
		
		$user = elgg_get_logged_in_user_entity();
		if ($user) {
			elgg_register_menu_item('page', [
				'name' => 'mentions',
				'href' => 'thewire/search/@' . $user->username,
				'text' => elgg_echo('thewire_tools:menu:mentions'),
				'context' => 'thewire',
			]);
		}
		
		elgg_register_menu_item('page', [
			'name' => 'search',
			'href' => 'thewire/search',
			'text' => elgg_echo('search'),
			'context' => 'thewire',
		]);
	}
}
