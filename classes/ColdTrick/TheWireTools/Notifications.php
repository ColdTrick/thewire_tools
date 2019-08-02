<?php

namespace ColdTrick\TheWireTools;

class Notifications {
	
	/**
	 * This functions performs actions when a wire post is created
	 *
	 * @param \Elgg\Event $event 'create', 'object'
	 *
	 * @return void
	 */
	public static function triggerMentionNotificationEvent(\Elgg\Event $event) {
		
		$object = $event->getObject();
		if (!$object instanceof \ElggWire) {
			return;
		}
		
		if (elgg_is_active_plugin('mentions')) {
			// mentions is better
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
	
			if (empty($user) || ($user->guid === $object->owner_guid)) {
				continue;
			}
				
			$setting = thewire_tools_get_notification_settings($user->guid);
			if (empty($setting)) {
				continue;
			}
	
			$subject = elgg_echo('thewire_tools:notify:mention:subject', [], $user->getLanguage());
			$message = elgg_echo('thewire_tools:notify:mention:message', [
				$user->getDisplayName(),
				$object->getOwnerEntity()->getDisplayName(),
				elgg_generate_url('collection:object:thewire:search', [
					'q' => "@{$user->username}",
				]),
			], $user->getLanguage());
	
			notify_user($user->guid, $object->owner_guid, $subject, $message, $params, $setting);
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
		
		if (elgg_is_active_plugin('mentions')) {
			// no settings to save if mentions plugin is enabled
			return;
		}
		
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
