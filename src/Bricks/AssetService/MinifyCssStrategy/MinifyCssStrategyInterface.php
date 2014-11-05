<?php

namespace Bricks\AssetService\MinifyCssStrategy;

use Bricks\AssetService\AssetModule;

interface MinifyCssStrategyInterface {
	
	public function minify(AssetModule $module);
	
}