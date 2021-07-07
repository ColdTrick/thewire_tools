<?php

namespace ColdTrick\TheWireTools;

class Notifications {
	
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
