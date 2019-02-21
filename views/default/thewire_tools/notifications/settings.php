<?php

$user = elgg_extract('user', $vars);
if (!$user instanceof ElggUser) {
	return;
}

if (elgg_is_active_plugin('mentions')) {
	// mentions is better
	return;
}

$methods = elgg_get_notification_methods();
if (empty($methods)) {
	return;
}

$method_options = [];
foreach ($methods as $method) {
	$label = elgg_echo("notification:method:$method");
	$method_options[$label] = $method;
}
?>
<div class="elgg-subscription-record">
	<div class="elgg-subscription-description">
		<?= elgg_echo('thewire_tools:usersettings:notify_mention') ?>
	</div>
	<?php
	$notification_settings = thewire_tools_get_notification_settings($user->guid);
	
	echo elgg_view_field([
		'#type' => 'checkboxes',
		'#class' => 'elgg-subscription-methods',
		'name' => 'thewire_tools',
		'options' => $method_options,
		'default' => false,
		'value' => $notification_settings,
		'align' => 'horizontal',
	]);
	?>
</div>
