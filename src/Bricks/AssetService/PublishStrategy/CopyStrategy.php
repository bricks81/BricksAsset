<?php

namespace Bricks\AssetService\PublishStrategy;

use Bricks\AssetService\PublishStrategy\PublishStrategyInterface;
use Bricks\AssetService\AssetModule;
use Bricks\AssetService\Adapter\AssetAdapterInterface;

/**
 * This adapter copies a file to the public resource directory
 */
class CopyStrategy implements PublishStrategyInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\PublishStrategy\PublishStrategyInterface::publish()
	 */
	public function publish(AssetModule $module){
		$adapter = $module->getAssetAdapter();
		$module_assets_path = $adapter->getModuleAssetsPath($module);
		$http_assets_path = $adapter->getHttpAssetsPath($module);
		if(false==$module_assets_path||false==$http_assets_path){
			return;
		}			
		$update = $this->getPublishUpdate($module_assets_path,$http_assets_path);			
		foreach($update AS $source => $target){
			$adapter->copy($source,$target);
		}		
	}
	
	/**
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getPublishUpdate(AssetAdapterInterface $adapter,$source,$target){
		$update = array();
		$filelist = $adapter->getSourceDirList($source);				
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$update += $this->getPublishUpdate($adapter,$_source,$_target);
			} elseif($adapter->isSourceFile($_source)){
				if(!$adapter->isTargetFile($_target)||$adapter->isOlderThan($_source,$_target)){
					$update[$_source] = $_target;
				}
			}
		}
		return $update;
	}
	
}