<?php
/**
 * Ajax wrapper view to show a wire add form
 */

$reshare_guid = (int) get_input("reshare_guid");
$reshare = get_entity($reshare_guid);
if (!empty($reshare) && !elgg_instanceof($reshare, "object")) {
	unset($reshare);
}

echo "<div id='thewire-tools-reshare-wrapper'>";
echo elgg_view_title(elgg_echo("thewire_tools:reshare"));
echo elgg_view_form("thewire/add", array(), array("reshare" => $reshare));
echo "</div>";