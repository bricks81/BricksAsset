<?php

namespace Bricks\Asset\ScssStrategy;

use Bricks\Asset\AssetModule;

interface ScssStrategyInterface {
	
	/**
	 * @param AssetModule $module
	 */
	public function scss(AssetModule $module);
	
}