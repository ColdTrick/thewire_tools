<?php

use ColdTrick\TheWireTools\Bootstrap;
use Elgg\Router\Middleware\Gatekeeper;

require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'plugin'> [
		'version' => '9.1.1',
		'dependencies' => [
			'thewire' => [
				'position' => 'after',
			],
		]
	],
	'bootstrap' => Bootstrap::class,
	'settings' => [
		'enable_group' => 'no',
		'extend_widgets' => 'yes',
		'extend_activity' => 'no',
	],
	'routes' => [
		'collection:object:thewire:group' => [
			'path' => '/thewire/group/{guid}',
			'resource' => 'thewire/group',
		],
		'collection:object:thewire:search' => [
			'path' => '/thewire/search/{q?}',
			'resource' => 'thewire/search',
		],
	],
	'widgets' => [
		'index_thewire' => [
			'context' => ['index'],
			'multiple' => true,
		],
		'thewire_post' => [
			'context' => ['index', 'dashboard'],
		],
	],
	'actions' => [
		'thewire/add' => [],
		'thewire_tools/toggle_feature' => [],
	],
	
	'view_extensions' => [
		'css/elgg' => [
			'css/thewire_tools.css' => [],
		],
		'page/layouts/elements/filter' => [
			'thewire_tools/group_activity' => ['priority' => 400],
		],
		'river/filter' => [
			'thewire_tools/activity_post' => ['priority' => 400],
		],
		'thewire/sidebar' => [
			'thewire_tools/extends/thewire/sidebar' => ['priority' => 400],
		],
	],
	'view_options' => [
		'thewire_tools/reshare' => ['ajax' => true],
		'thewire_tools/reshare_list' => ['ajax' => true],
		'thewire_tools/thread' => ['ajax' => true],
	],
];
