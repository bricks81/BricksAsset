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
		$path = realpath($module->getWwwRootPath().'/'.$module->getHttpAssetsPath().'/'.$module->getModuleName());
		if(false==$path){
			return;
		}		
		$adapter->removeTargetDir($path);
	}
	
}