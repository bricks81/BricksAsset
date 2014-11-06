<?php

namespace Bricks\AssetService\MinifyJsStrategy;

use \JSMin;
use Bricks\AssetService\AssetModule;
use Bricks\AssetService\Adapter\AssetAdapterInterface;

class MrclayMinifyStrategy implements MinifyJsStrategyInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\MinifyJsStrategy\MinifyStrategyInterface::minify()
	 */
	public function minify(AssetModule $module){
		$adapter = $module->getAssetAdapter();
		$http_assets_path = $adapter->getHttpAssetsPath($module);
		if(false==$http_assets_path){
			return;
		}
		$update = $this->getMinifyUpdate($adapter,$source,$target);
		foreach($update AS $source => $target){
			$content = $adapter->readSourceFile($source);
			$content = JSMin::minify($content);
			$adapter->writeTargetFile($target,$content);	
		}		
	}	
	
	protected function getMinifyUpdate(AssetAdapterInterface $adapter,$source,$target){
		$update = array();
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$update += $this->getMinifyUpdate($adapter,$_source,$_target);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-3)!='.js'||substr($_source,-7)=='.min.js'){
					continue;
				}
				$_target = substr($_target,0,strlen($_target)-3).'.min.js';
				if(!$adapter->isTargetFile($_target)||$adapter->isOlderThan($_source, $_target)){
					$update[$_source] = $_target;
				}
			}
		}
		return $update;
	}
	
}