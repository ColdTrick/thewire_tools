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
	
	/**
	 * Change the notification for wire posts that are reshares
	 *
	 * @param \Elgg\Hook $hook 'prepare', 'notification:create:object:thewire'
	 *
	 * @return void|\Elgg\Notifications\Notification
	 */
	public static function prepareReshareNotification(\Elgg\Hook $hook) {
		
		/* @var $event \Elgg\Notifications\Event */
		$event = $hook->getParam('event');
		$entity = $event->getObject();
		if (!$entity instanceof \ElggWire) {
			return;
		}
		$shared_entities = $entity->getEntitiesFromRelationship([
			'limit' => 1,
			'relationship' => 'reshare',
		]);
		if (empty($shared_entities)) {
			return;
		}
		$shared_entity = $shared_entities[0];
		
		$actor = $event->getActor();
		$language = $hook->getParam('language');
		
		/* @var $notification \Elgg\Notifications\Notification */
		$notification = $hook->getValue();
		
		if (elgg_language_key_exists("item:{$shared_entity->getType()}:{$shared_entity->getSubtype()}")) {
			$type = elgg_echo("item:{$shared_entity->getType()}:{$shared_entity->getSubtype()}", [], $language);
		} else {
			$type = elgg_echo('unknown', [], $language);
		}
		
		$title = $shared_entity->getDisplayName() ?: $entity->description;
		$title = elgg_get_excerpt($title, 25);
		
		$notification->subject = elgg_echo('thewire_tools:notify:reshare:subject', [
			$actor->getDisplayName(),
			$type,
			$title,
		], $language);
		$notification->summary = elgg_echo('thewire_tools:notify:reshare:summary', [
			$actor->getDisplayName(),
			$type,
			$title,
		], $language);
		$notification->body = elgg_echo('thewire_tools:notify:reshare:body', [
			$actor->getDisplayName(),
			$entity->description,
			$shared_entity->getURL(),
			$entity->getURL(),
		], $language);
		
		return $notification;
	}
	
	/**
	 * Change the notification subject for wire posts that are not reshares
	 *
	 * @param \Elgg\Hook $hook 'prepare', 'notification:create:object:thewire'
	 *
	 * @return void|\Elgg\Notifications\Notification
	 */
	public static function prepareNotificationSubject(\Elgg\Hook $hook) {
		
		/* @var $event \Elgg\Notifications\Event */
		$event = $hook->getParam('event');
		$entity = $event->getObject();
		if (!$entity instanceof \ElggWire) {
			return;
		}
		$shared_entities = $entity->getEntitiesFromRelationship([
			'limit' => 1,
			'relationship' => 'reshare',
		]);
		if (!empty($shared_entities)) {
			return;
		}
		
		$language = $hook->getParam('language');
		
		/* @var $notification \Elgg\Notifications\Notification */
		$notification = $hook->getValue();
		
		$notification->subject = elgg_echo('thewire:notify:summary', [
			$entity->getDisplayName(),
		], $language);
		
		return $notification;
	}
}
