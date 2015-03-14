<?php

namespace Bricks\Asset\PublishStrategy;

use Bricks\Asset\PublishStrategy\PublishStrategyInterface;
use Bricks\Asset\AssetModule;
use Bricks\Asset\AssetAdapter\AssetAdapterInterface;

/**
 * This adapter copies a file to the public resource directory
 */
class CopyStrategy implements PublishStrategyInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\PublishStrategy\PublishStrategyInterface::publish()
	 */
	public function publish(AssetModule $module){
		$adapter = $module->getAssetAdapter();		
		$modulePath = $module->getModuleAssetsPath();
		$path = realpath($module->getWwwRootPath().'/'.$module->getHttpAssetsPath()).'/'.$module->getModuleName();
		if(false==$modulePath||false==$path){
			return;
		}			
		$update = $this->getPublishUpdate($adapter,$modulePath,$path);
		
		// exclude patterns
		$exclude = $module->getExclude();
		if(0<count($exclude)){
			foreach($update AS $source => $target){
				foreach($exclude AS $expression){
					if(preg_match($expression,$source)){
						unset($update[$source]);
					}
				}
			}
		}
		
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
			$_source = $source.'/'.$filename;			
			$_target = $target.'/'.$filename;
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