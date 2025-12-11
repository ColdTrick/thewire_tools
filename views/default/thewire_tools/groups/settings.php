<?php
/**
 * Group specific settings for TheWire (Tools)
 */

if (elgg_get_plugin_setting('enable_group', 'thewire_tools') !== 'yes') {
	// not enabled for groups
	return;
}

/** @var null|\ElggGroup $group */
$group = elgg_extract('entity', $vars);

// allow re-sharing in this group
$checked = true;
if ($group instanceof \ElggGroup) {
	$checked = (bool) $group->getPluginSetting('thewire_tools', 'enable_reshare', true);
}

$settings = elgg_view_field([
	'#type' => 'switch',
	'#label' => elgg_echo('thewire_tools:group_settings:enable_reshare'),
	'#help' => elgg_echo('thewire_tools:group_settings:enable_reshare:help'),
	'name' => 'settings[thewire_tools][enable_reshare]',
	'value' => $checked,
]);

echo elgg_view_module('info', elgg_echo('thewire_tools:group_settings:title'), $settings);
