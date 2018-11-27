<?php

namespace ColdTrick\TheWireTools;

class Notifications {
	
	/**
	 * This functions performs actions when a wire post is created
	 *
	 * @param string      $event  'create'
	 * @param string      $type   'object'
	 * @param \ElggObject $object the ElggObject created
	 *
	 * @return void
	 */
	public static function triggerMentionNotificationEvent($event, $type, \ElggObject $object) {
		
		if (!$object instanceof \ElggWire) {
			return;
		}
		
		// @todo replace with decent Elgg 2.0 notification event handling
	
		//send out notification to users mentioned in a wire post
		$usernames = [];
		preg_match_all("/\@([A-Za-z0-9\_\.\-]+)/i", $object->description, $usernames);
	
		if (empty($usernames)) {
			return;
		}
	
		$usernames = array_unique($usernames[0]);
		$params = [
			'object' => $object,
			'action' => 'mention',
		];
	
		foreach ($usernames as $username) {
			$username = str_ireplace('@', '', $username);
			$user = get_user_by_username($username);
	
			if (empty($user) || ($user->getGUID() == $object->getOwnerGUID())) {
				continue;
			}
				
			$setting = thewire_tools_get_notification_settings($user->getGUID());
			if (empty($setting)) {
				continue;
			}
	
			$subject = elgg_echo('thewire_tools:notify:mention:subject');
			$message = elgg_echo('thewire_tools:notify:mention:message', [
				$user->name,
				$object->getOwnerEntity()->name,
				elgg_normalize_url("thewire/search/@{$user->username}"),
			]);
	
			notify_user($user->getGUID(), $object->getOwnerGUID(), $subject, $message, $params, $setting);
		}
	}
	
	/**
	 * Save thewire_tools preferences for the user
	 *
	 * @param \Elgg\Hook $hook 'action', 'notifications/settings'
	 *
	 * @return void
	 */
	public static function saveUserNotificationsSettings(\Elgg\Hook $hook) {
		
		$user_guid = (int) get_input('guid');
		if (empty($user_guid)) {
			return;
		}
		
		$user = get_user($user_guid);
		if (empty($user) || !$user->canEdit()) {
			return;
		}
		
		$methods = (array) get_input('thewire_tools');
		
		if (!empty($methods)) {
			elgg_set_plugin_user_setting('notification_settings', implode(',', $methods), $user->guid, 'thewire_tools');
		} else {
			elgg_unset_plugin_user_setting('notification_settings', $user->guid, 'thewire_tools');
		}
	
		// set flag for correct fallback behaviour
		elgg_set_plugin_user_setting('notification_settings_saved', '1', $user->guid, 'thewire_tools');
	}
}
