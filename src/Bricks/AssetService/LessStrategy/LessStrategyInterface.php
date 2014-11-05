<?php

namespace Bricks\AssetService\LessStrategy;

use Bricks\AssetService\AssetModule;

interface LessStrategyInterface {

	public function less(AssetModule $module);
	
}