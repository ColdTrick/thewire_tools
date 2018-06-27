<?php

require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'bootstrap' => '\ColdTrick\TheWireTools\Bootstrap',
	'settings' => [
		'enable_group' => 'no',
		'extend_widgets' => 'yes',
		'extend_activity' => 'no',
		'mention_display' => 'username',
		
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
