<?php

namespace Bricks\Asset\MinifyJsStrategy;

use Bricks\Asset\AssetModule;

interface MinifyJsStrategyInterface {
	
	public function minify(AssetModule $module);
	
}