<?php

namespace ColdTrick\TheWireTools;

use Elgg\Groups\ToolContainerLogicCheck;

/**
 * Prevent wire posts from being created if the group tool option is disabled
 */
class GroupToolContainerLogicCheck extends ToolContainerLogicCheck {

	/**
	 * {@inheritDoc}
	 */
	public function getContentType(): string {
		return 'object';
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getContentSubtype(): string {
		return 'thewire';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getToolName(): string {
		return 'thewire';
	}
}
