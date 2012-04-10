<?php
/**
 * Wire add form body
 *
 * @uses $vars['post']
 */

elgg_load_js('elgg.thewire');

$post = elgg_extract('post', $vars);

$text = elgg_echo('post');
if ($post) {
	$text = elgg_echo('thewire:reply');
}

if ($post) {
	echo elgg_view('input/hidden', array(
		'name' => 'parent_guid',
		'value' => $post->guid,
	));
}

echo elgg_view('input/plaintext', array(
	'name' => 'body',
	'class' => 'mtm',
	'id' => 'thewire-textarea',
));
?>
<div id="thewire-characters-remaining">
	<span>140</span> <?php echo elgg_echo('thewire:charleft'); ?>
</div>
<div class="elgg-foot mts">
<?php

echo elgg_view('input/submit', array(
	'value' => $text,
	'id' => 'thewire-submit-button',
));

if(elgg_get_plugin_setting("enable_group", "thewire_tools") == "yes"){
	$page_owner_entity = elgg_get_page_owner_entity();
	if($post){
		echo elgg_view('input/hidden', array("name" => "access_id", "value" => $post->access_id));
	} else {
		if($page_owner_entity instanceof ElggGroup){
			// in a group only allow sharing in the current group
			echo elgg_view('input/hidden', array("name" => "access_id", "value" => $page_owner_entity->group_acl));
		} else {
			echo elgg_view('input/access', array("name" => "access_id"));
		}	
	}
}

?>
</div>