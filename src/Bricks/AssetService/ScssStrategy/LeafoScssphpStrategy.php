<?php

namespace Bricks\AssetService\ScssStrategy;

use Leafo\ScssPhp\Compiler;
use Bricks\AssetService\AssetModule;
use Bricks\AssetService\ScssStrategy\ScssStrategyInterface;

class LeafoScssphpStrategy implements ScssStrategyInterface {

	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\ScssStrategy\ScssStrategyInterface::scss()
	 */
	public function scss(AssetModule $module){
		$adapter = $module->getAssetAdapter();
		$http_assets_path = $adapter->getHttpAssetsPath($module);
		if(false==$http_assets_path){
			return;
		}
		$update = $this->getScssUpdate($adapter,$source,$target);
		foreach($update AS $source => $target){
			$content = $adapter->readSourceFile($source);				
			$scss = new Compiler();
			$content = $scss->compile($content);
			$adapter->writeTargetFile($target,$content);
		}
	}
	
	/**
	 * @param AssetAdapterInterface
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getScssUpdate(AssetAdapterInterface $adapter,$source,$target){
		$update = array();
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$update += $this->getScssUpdate($adapter,$_source,$_target);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-5)!='.scss'&&substr($_source,-5)!='.sass'){
					continue;
				}
				if(substr($_source,-9)=='.scss.css'||substr($_source,-9)=='.sass.css'){
					continue;
				}
				$_target = $_target.'.css';
				if(!$adapter->isTargetFile($_target)||$adapter->isOlderThan($_source,$_target)){
					$update[$_source] = $_target;
				}
			}
		}
		return $update;
	}
	
}