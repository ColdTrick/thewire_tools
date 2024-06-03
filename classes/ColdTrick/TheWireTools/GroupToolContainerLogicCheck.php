<?php

namespace ColdTrick\TheWireTools;

use Elgg\Groups\ToolContainerLogicCheck;

/**
 * Prevent wire posts from being created if the group tool option is disabled
 */
class GroupToolContainerLogicCheck extends ToolContainerLogicCheck {

	/**
	 * {@inheritdoc}
	 */
	public function getContentType(): string {
		return 'object';
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getContentSubtype(): string {
		return 'thewire';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getToolName(): string {
		return 'thewire';
	}
}
