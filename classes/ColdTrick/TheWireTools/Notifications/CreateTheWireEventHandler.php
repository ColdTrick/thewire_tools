<?php

namespace ColdTrick\TheWireTools\Notifications;

use Elgg\TheWire\Notifications\CreateTheWireEventHandler as CoreTheWireEventHandler;

/**
 * Notification Event Handler for 'object' 'thewire' 'create' action
 *
 * Change the notification in case of a reshare wire post
 */
class CreateTheWireEventHandler extends CoreTheWireEventHandler {
	
	/**
	 * @var \ElggEntity
	 */
	protected $reshare_entity;
	
	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationSubject(\ElggUser $recipient, string $method): string {
		$shared_entity = $this->getReshareEntity();
		if (empty($shared_entity)) {
			return parent::getNotificationSummary($recipient, $method);
		}
		
		if (elgg_language_key_exists("item:{$shared_entity->getType()}:{$shared_entity->getSubtype()}")) {
			$type = elgg_echo("item:{$shared_entity->getType()}:{$shared_entity->getSubtype()}", [], $recipient->getLanguage());
		} else {
			$type = elgg_echo('unknown', [], $recipient->getLanguage());
		}
		
		$title = $shared_entity->getDisplayName() ?: $this->event->getObject()->description;
		$title = elgg_get_excerpt($title, 25);
		
		return elgg_echo('thewire_tools:notify:reshare:subject', [
			$this->event->getActor()->getDisplayName(),
			$type,
			$title,
		], $recipient->getLanguage());
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationSummary(\ElggUser $recipient, string $method): string {
		$shared_entity = $this->getReshareEntity();
		if (empty($shared_entity)) {
			return parent::getNotificationSummary($recipient, $method);
		}
		
		if (elgg_language_key_exists("item:{$shared_entity->getType()}:{$shared_entity->getSubtype()}")) {
			$type = elgg_echo("item:{$shared_entity->getType()}:{$shared_entity->getSubtype()}", [], $recipient->getLanguage());
		} else {
			$type = elgg_echo('unknown', [], $recipient->getLanguage());
		}
		
		$title = $shared_entity->getDisplayName() ?: $this->event->getObject()->description;
		$title = elgg_get_excerpt($title, 25);
		
		return elgg_echo('thewire_tools:notify:reshare:summary', [
			$this->event->getActor()->getDisplayName(),
			$type,
			$title,
		], $recipient->getLanguage());
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationBody(\ElggUser $recipient, string $method): string {
		$shared_entity = $this->getReshareEntity();
		if (empty($shared_entity)) {
			return parent::getNotificationBody($recipient, $method);
		}
		
		/* @var $entity \ElggWire */
		$entity = $this->event->getObject();
		
		return elgg_echo('thewire_tools:notify:reshare:body', [
			$this->event->getActor()->getDisplayName(),
			$entity->description,
			$shared_entity->getURL(),
			$entity->getURL(),
		], $recipient->getLanguage());
	}
	
	/**
	 * Returns the related reshared entity
	 *
	 * @return \ElggEntity|null
	 */
	protected function getReshareEntity(): ?\ElggEntity {
		if (!isset($this->reshare_entity)) {
			$reshare_entities = $this->event->getObject()->getEntitiesFromRelationship([
				'limit' => 1,
				'relationship' => 'reshare',
			]);
		
			if (empty($reshare_entities)) {
				$this->reshare_entity = false;
			} else {
				$this->reshare_entity = $reshare_entities[0];
			}
		}
		
		if (!$this->reshare_entity instanceof \ElggEntity) {
			return null;
		}
		
		return $this->reshare_entity;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected static function isConfigurableForGroup(\ElggGroup $group): bool {
		return $group->isToolEnabled('thewire');
	}
}
