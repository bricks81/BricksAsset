<?php

namespace Bricks\AssetService\ScssStrategy;

use Bricks\AssetService\AssetModule;

interface ScssStrategyInterface {
	
	/**
	 * @param AssetModule $module
	 */
	public function scss(AssetModule $module);
	
}