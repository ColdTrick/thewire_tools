<?php

echo elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'fields' => [
		[
			'#type' => 'text',
			'#class' => 'elgg-field-stretch',
			'name' => 'q',
			'value' => elgg_extract('query', $vars),
		],
		[
			'#type' => 'submit',
			'value' => elgg_echo('search'),
		],
	],
]);
	