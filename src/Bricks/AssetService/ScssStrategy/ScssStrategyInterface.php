<?php

namespace Bricks\AssetService\ScssStrategy;

interface ScssStrategyInterface {
	
	/**
	 * @param AssetModule $module
	 */
	public function scss(AssetModule $module);
	
}