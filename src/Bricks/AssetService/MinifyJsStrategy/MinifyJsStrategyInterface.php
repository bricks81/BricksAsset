<?php

namespace Bricks\AssetService\MinifyJsStrategy;

use Bricks\AssetService\AssetModule;

interface MinifyJsStrategyInterface {
	
	public function minify(AssetModule $module);
	
}