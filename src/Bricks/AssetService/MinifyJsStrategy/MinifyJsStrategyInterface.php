<?php

namespace Bricks\AssetService\MinifyJsStrategy;

interface MinifyJsStrategyInterface {
	
	public function minify(AssetModule $module);
	
}