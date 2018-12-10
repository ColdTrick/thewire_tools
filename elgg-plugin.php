<?php

use Elgg\Router\Middleware\Gatekeeper;

require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'bootstrap' => '\ColdTrick\TheWireTools\Bootstrap',
	'settings' => [
		'enable_group' => 'no',
		'extend_widgets' => 'yes',
		'extend_activity' => 'no',
		'mention_display' => 'username',
		
	],
	'routes' => [
		'collection:object:thewire:group' => [
			'path' => '/thewire/group/{guid}',
			'resource' => 'thewire/group',
		],
		'collection:object:thewire:autocomplete' => [
			'path' => '/thewire/autocomplete',
			'resource' => 'thewire/autocomplete',
			'middleware' => [
				Gatekeeper::class,
			],
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
];
