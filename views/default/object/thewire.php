<?php
/**
 * View a wire post
 * 
 * @uses $vars['entity']
 */

elgg_load_js('elgg.thewire');

$full = elgg_extract('full_view', $vars, FALSE);
$post = elgg_extract('entity', $vars, FALSE);

if (!$post) {
	return true;
}

// make compatible with posts created with original Curverider plugin
$thread_id = $post->wire_thread;
if (!$thread_id) {
	$post->wire_thread = $post->guid;
}

$owner = $post->getOwnerEntity();

$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$owner_link = elgg_view('output/url', array(
	'href' => "thewire/owner/$owner->username",
	'text' => $owner->name,
	'is_trusted' => true,
));
$author_text = elgg_echo('byline', array($owner_link));
$date = elgg_view_friendly_time($post->time_created);

$metadata = elgg_view_menu('entity', array(
	'entity' => $post,
	'handler' => 'thewire',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "$author_text $date";

// check if need to show group
if(($post->owner_guid != $post->container_guid) && (elgg_get_page_owner_guid() != $post->container_guid)){
	$group = get_entity($vars["entity"]->container_guid);
	$group_link = elgg_view("output/url", array("href" => "thewire/group/" . $group->getGUID(), "text" => $group->name, "class" => "thewire_tools_object_link"));
	$subtitle .= " " . elgg_echo("river:ingroup", array($group_link));
}
// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

$params = array(
	'entity' => $post,
	'metadata' => $metadata,
	'subtitle' => $subtitle,
	'content' => thewire_filter($post->description),
	'tags' => false,
);
$params = $params + $vars;
$list_body = elgg_view('object/elements/summary', $params);

echo elgg_view_image_block($owner_icon, $list_body);

if ($post->reply) {
	echo "<div class=\"thewire-parent hidden\" id=\"thewire-previous-{$post->guid}\">";
	echo "</div>";
}
