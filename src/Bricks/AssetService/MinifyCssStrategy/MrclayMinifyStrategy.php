<?php

namespace Bricks\AssetService\MinifyCssStrategy;

use \CSSmin;
use Bricks\AssetService\AssetModule;
use Bricks\AssetService\MinifyCssStrategy\MinifyCssStrategyInterface;
use Bricks\AssetService\Adapter\AssetAdapterInterface;

class MrclayMinifyStrategy implements MinifyCssStrategyInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\MinifyCssStrategy\MinifyStrategyInterface::minify()
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
			$min = new CSSmin();
			$content = $min->run($content);
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
				if(substr($_source,-4)!=='.css'||substr($_source,-8)=='.min.css'){
					continue;
				}
				$_target = substr($_target,0,strlen($_target)-4).'.min.css';
				if(!$adapter->isTargetFile($_target)||$adapter->isOlderThan($_source,$_target)){
					$update[$_source] = $_target;
				}
			}
		}
		return $update;
	}
	
}