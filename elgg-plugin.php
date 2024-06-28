<?php

use ColdTrick\TheWireTools\Notifications\CreateTheWireEventHandler;
use Elgg\Blog\GroupToolContainerLogicCheck;
use Elgg\Router\Middleware\GroupPageOwnerGatekeeper;

require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'plugin' => [
		'version' => '14.0',
		'dependencies' => [
			'thewire' => [
				'position' => 'after',
			],
		]
	],
	'settings' => [
		'enable_group' => 'no',
		'extend_widgets' => 'yes',
		'extend_activity' => 'no',
	],
	'actions' => [
		'thewire/add' => [],
		'thewire_tools/toggle_feature' => [],
	],
	'events' => [
		'container_logic_check' => [
			'object' => [
				\ColdTrick\TheWireTools\GroupToolContainerLogicCheck::class => [],
			],
		],
		'entity:url' => [
			'object:widget' => [
				'\ColdTrick\TheWireTools\Widgets::widgetTitleURL' => [],
			],
		],
		'export:counters' => [
			'opensearch' => [
				'\ColdTrick\TheWireTools\Plugins\OpenSearch::exportCounter' => [],
			],
		],
		'group_tool_widgets' => [
			'widget_manager' => [
				'\ColdTrick\TheWireTools\Plugins\WidgetManager::groupToolBasedWidgets' => [],
			],
		],
		'handlers' => [
			'widgets' => [
				'\ColdTrick\TheWireTools\Plugins\Groups::registerWidget' => [],
			],
		],
		'register' => [
			'menu:entity' => [
				'\ColdTrick\TheWireTools\Menus\Entity::registerFeature' => [],
				'\ColdTrick\TheWireTools\Menus\Entity::registerImprove' => ['priority' => 501],
			],
			'menu:owner_block' => [
				'\ColdTrick\TheWireTools\Menus\OwnerBlock::registerGroup' => [],
			],
			'menu:social' => [
				'\ColdTrick\TheWireTools\Menus\Social::registerReshare' => [],
			],
		],
		'supported_types' => [
			'entity_tools' => [
				'\ColdTrick\TheWireTools\Plugins\EntityTools::registerTheWire' => [],
			],
		],
		'tool_options' => [
			'group' => [
				'\ColdTrick\TheWireTools\Plugins\Groups::registerGroupTool' => [],
			],
		],
	],
	'notifications' => [
		'object' => [
			'thewire' => [
				'create' => CreateTheWireEventHandler::class,
			],
		],
	],
	'routes' => [
		'collection:object:thewire:group' => [
			'path' => '/thewire/group/{guid}',
			'resource' => 'thewire/group',
			'middleware' => [
				GroupPageOwnerGatekeeper::class,
			],
		],
	],
	'view_extensions' => [
		'elgg.css' => [
			'forms/thewire/add.css' => [],
			'thewire_tools/thread.css' => [],
		],
		'forms/thewire/add.css' => [
			'thewire_tools/extends/forms/thewire/add.css' => [],
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
		'thewire_tools/reply_loader' => ['ajax' => true],
		'thewire_tools/reshare' => ['ajax' => true],
		'thewire_tools/reshare_list' => ['ajax' => true],
		'thewire_tools/thread' => ['ajax' => true],
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
];
