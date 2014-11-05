<?php

namespace BricksAssetTest\Mock\PublishStrategy;

use Bricks\AssetService\PublishStrategy\PublishStrategyInterface;

class NullStrategy implements PublishStrategyInterface {
	
	public function __construct(){}
	
	public function publish($source,$target){}
	
}