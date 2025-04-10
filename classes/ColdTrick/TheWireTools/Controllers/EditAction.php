<?php

namespace ColdTrick\TheWireTools\Controllers;

use Elgg\Exceptions\Http\ValidationException;

/**
 * Additions to the core TheWire EditAction
 * - allow group content / access
 * - link reshared content
 */
class EditAction extends \Elgg\TheWire\Controllers\EditAction {
	
	/**
	 * {@inheritdoc}
	 */
	protected function validate(): void {
		parent::validate();
		
		// validate container guid (to allow for group content)
		if (elgg_get_plugin_setting('enable_group', 'thewire_tools') !== 'yes') {
			$this->request->setParam('container_guid', null);
		} else {
			$container_guid = (int) $this->request->getParam('container_guid');
			
			$group = get_entity($container_guid);
			if ($group instanceof \ElggGroup) {
				if (!$group->isToolEnabled('thewire')) {
					// not allowed to post in this group
					throw new ValidationException(elgg_echo('thewire_tools:groups:error:not_enabled'));
				}
			}
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function execute(array $skip_field_names = []): void {
		$skip_field_names[] = 'access_id';
		
		parent::execute($skip_field_names);
		
		// save access ID
		$this->entity->access_id = $this->getAccessID();
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function executeAfter(): void {
		parent::executeAfter();
		
		// save reshare guid
		$reshare_guid = (int) $this->request->getParam('reshare_guid');
		if ($reshare_guid > 0) {
			$this->entity->addRelationship($reshare_guid, 'reshare');
		}
	}
	
	/**
	 * Get the correct access_id for content in groups etc
	 *
	 * @return int
	 */
	protected function getAccessID(): int {
		$access_id = (int) $this->request->getParam('access_id', ACCESS_PUBLIC);
		if ($access_id === -100) {
			$access_id = null;
		}
		
		if (elgg_get_plugin_setting('enable_group', 'thewire_tools') === 'yes') {
			$container_guid = (int) $this->request->getParam('container_guid');
			
			$group = get_entity($container_guid);
			if ($group instanceof \ElggGroup) {
				$acl = $group->getOwnedAccessCollection('group_acl');
				if ($acl instanceof \ElggAccessCollection) {
					if (is_null($access_id) || $group->getContentAccessMode() === \ElggGroup::CONTENT_ACCESS_MODE_MEMBERS_ONLY) {
						$access_id = $acl->id;
					}
				}
			}
		}
		
		// check the access id
		if ($access_id === ACCESS_PRIVATE) {
			// private wire posts aren't allowed
			$access_id = ACCESS_LOGGED_IN;
		}
		
		if (is_null($access_id)) {
			$access_id = ACCESS_PUBLIC;
		}
		
		return $access_id;
	}
}
