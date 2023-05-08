<?php

$thread_guid = (int) get_input('thread');
$guid = (int) get_input('guid');

if (empty($thread_guid) || empty($guid)) {
	return;
}

elgg_push_context('thewire_tools_thread');

echo elgg_list_entities([
	'type' => 'object',
	'subtype' => 'thewire',
	'base_url' => elgg_generate_url('collection:object:thewire:thread', [
		'guid' => $thread_guid,
	]),
	'metadata_name_value_pairs' => [
		'name' => 'wire_thread',
		'value' => $thread_guid,
		'case_sensitive' => false,
		'type' => ELGG_VALUE_INTEGER,
	],
]);

elgg_pop_context();
