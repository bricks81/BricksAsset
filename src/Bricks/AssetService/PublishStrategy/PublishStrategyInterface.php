<?php

namespace Bricks\AssetService\PublishStrategy;

use Bricks\AssetService\AssetModule;
interface PublishStrategyInterface {
	
	public function publish(AssetModule $module);
	
}