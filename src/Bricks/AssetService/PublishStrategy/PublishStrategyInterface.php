<?php

namespace Bricks\AssetService\PublishStrategy;

use Bricks\AssetService\AssetModule;
interface PublishStrategyInterface {
	
	/**
	 * @param AssetModule $module
	 */
	public function publish(AssetModule $module);
	
}