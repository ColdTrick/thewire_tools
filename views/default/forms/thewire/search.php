<?php

echo elgg_view_field([
	'#type' => 'text',
	'name' => 'q',
	'value' => elgg_extract('query', $vars),
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('search'),
]);

elgg_set_form_footer($footer);
	