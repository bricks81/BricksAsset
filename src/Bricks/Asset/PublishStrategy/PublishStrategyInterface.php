<?php

namespace Bricks\Asset\PublishStrategy;

use Bricks\Asset\AssetModule;

interface PublishStrategyInterface {
	
	/**
	 * @param AssetModule $module
	 */
	public function publish(AssetModule $module);
	
}