<?php
/*
 * Show The Wire form in activity or in group activity, if enabled in settings
 */
if (elgg_is_logged_in()) {	 
	// for user activity check settings and context equal to activity
	$context = (elgg_get_context() == 'activity' 
				&& elgg_get_plugin_setting("extend_activity", "thewire_tools") == "yes")?'activity':'';		
	if ($context=='' && elgg_get_context() == 'groups') {
		$owner_entity = elgg_get_page_owner_entity();		
		if ($owner_entity)	{			
			// for user activity check settings, context equal to group and page requested equal to activity/<group_GUID>
			if ($owner_entity->getType() == 'group') {				
				$page = $_GET['page'];
				$context = ($page == "activity/".$owner_entity->getGUID() 
						&&  elgg_get_plugin_setting("extend_group_activity", "thewire_tools") == "yes"
						&&  elgg_get_plugin_setting("enable_group", "thewire_tools") == "yes")?'group':'';
			}
		}
	}
	if ($context!='') {			
			echo elgg_view_form("thewire/add",$vars,array('thewiretools_label'=>$context)); 
	}
}
