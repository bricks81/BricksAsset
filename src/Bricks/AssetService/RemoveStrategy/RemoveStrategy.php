<?php

namespace Bricks\AssetService\RemoveStrategy;

use Bricks\AssetService\AssetModule;
use Bricks\AssetService\RemoveStrategy\RemoveStrategyInterface;

class RemoveStrategy implements RemoveStrategyInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\RemoveStrategy\RemoveStrategyInterface::remove()
	 */
	public function remove(AssetModule $module){
		$adapter = $module->getAssetAdapter();
		if(false==$adapter->getHttpAssetsPath($module)){
			return;
		}		
		$adapter->removeHttpAssetsPath($module);
	}
	
}