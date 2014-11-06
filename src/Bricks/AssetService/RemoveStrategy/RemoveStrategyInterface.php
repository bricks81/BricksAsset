<?php

namespace Bricks\AssetService\RemoveStrategy;

use Bricks\AssetService\AssetModule;

interface RemoveStrategyInterface {
	
	public function remove(AssetModule $module);
	
}