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
$container = $post->getContainerEntity();
$subtitle = array();

$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$owner_link = elgg_view('output/url', array(
	'href' => "thewire/owner/$owner->username",
	'text' => $owner->name,
	'is_trusted' => true,
));
$subtitle[] = elgg_echo("byline", array($owner_link));
$subtitle[] = elgg_view_friendly_time($post->time_created);

$metadata = elgg_view_menu('entity', array(
	'entity' => $post,
	'handler' => 'thewire',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

// check if need to show group
if (elgg_instanceof($container, "group") && ($container->getGUID() != elgg_get_page_owner_guid())) {
	$group_link = elgg_view("output/url", array(
		"href" => "thewire/group/" . $container->getGUID(),
		"text" => $container->name,
		"class" => "thewire_tools_object_link"
	));
	
	$subtitle[] = elgg_echo("river:ingroup", array($group_link));
}

// show text different in widgets
$text = $post->description;
if (elgg_in_context("widgets")) {
	$text = elgg_get_excerpt($text, 140);
	
	// show more link?
	if (substr($text, -3) == "...") {
		$text .= "&nbsp;" . elgg_view("output/url", array(
			"text" => elgg_echo("more"),
			"href" => $post->getURL(),
			"is_trusted" => true
		));
	}
}

$params = array(
	'entity' => $post,
	'metadata' => $metadata,
	'subtitle' => implode(" ", $subtitle),
	'content' => thewire_filter($text),
	'tags' => false,
);
$params = $params + $vars;
$list_body = elgg_view('object/elements/summary', $params);

echo elgg_view_image_block($owner_icon, $list_body);

if ($post->reply) {
	echo "<div class=\"thewire-parent hidden\" id=\"thewire-previous-{$post->guid}\">";
	echo "</div>";
}
