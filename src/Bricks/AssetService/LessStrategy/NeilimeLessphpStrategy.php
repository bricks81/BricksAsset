<?php

namespace Bricks\AssetService\LessStrategy;

use \lessc;
use Bricks\AssetService\Adapter\AdapterProvider;
use Bricks\AssetService\Adapter\AssetAdapterInterface;

class NeilimeLessphpStrategy {
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\LessStrategy\LessStrategyInterface::less()
	 */
	public function less(AssetModule $module){
		$adapter = $module->getAssetAdapter();
		$http_assets_path = $adapter->getHttpAssetsPath($module);
		if(false==$http_assets_path){
			return;
		}
		$update = $this->getLessUpdate($adapter,$http_assets_path,$http_assets_path);
		foreach($update AS $source => $target){
			$content = $adapter->readSourceFile($source);
			$lessc = new lessc();
			$content = $lessc->compile($content);
			$adapter->writeTargetFile($target,$content);
		}		
	}
	
	/**
	 * @param AssetAdapterInterface $adapter
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getLessUpdate(AssetAdapterInterface $adapter,$source,$target){
		$update = array();		
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$update += $this->getLessUpdate($adapter,$_source,$_target);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-5)!='.less'||substr($_source,-9)=='.less.css'){
					continue;
				}
				$_target .= '.css';
				if(!$adapter->isTargetFile($_target)||$adapter->isOlderThan($_source,$_target)){
					$update[$_source] = $_target;
				}
			}
		}
		return $update;
	}
	
	
}