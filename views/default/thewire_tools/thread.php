<?php

$thead_guid = (int) get_input('thread');
$guid = (int) get_input('guid');

if (empty($thead_guid) || empty($guid)) {
	return;
}

elgg_push_context('thewire_tools_thread');
echo elgg_list_entities_from_metadata([
	'type' => 'object',
	'subtype' => 'thewire',
	'metadata_name_value_pairs' => [
		'name' => 'wire_thread',
		'value' => $thead_guid,
	],
]);
elgg_pop_context();