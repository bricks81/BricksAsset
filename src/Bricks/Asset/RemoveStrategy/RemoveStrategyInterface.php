<?php

namespace Bricks\Asset\RemoveStrategy;

use Bricks\Asset\AssetModule;

interface RemoveStrategyInterface {
	
	public function remove(AssetModule $module);
	
}