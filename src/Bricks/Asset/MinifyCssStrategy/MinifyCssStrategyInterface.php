<?php

namespace Bricks\Asset\MinifyCssStrategy;

use Bricks\Asset\AssetModule;

interface MinifyCssStrategyInterface {
	
	public function minify(AssetModule $module);
	
}