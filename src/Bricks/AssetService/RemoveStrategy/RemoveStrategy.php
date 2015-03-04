<?php

namespace Bricks\AssetService\RemoveStrategy;

use Bricks\AssetService\AssetModule;
use Bricks\AssetService\RemoveStrategy\RemoveStrategyInterface;
use Bricks\AssetService\AssetAdapter\AssetAdapterInterface;

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
		
		$files = $this->getRemoveFiles($adapter,$path);		
		$exclude = $module->getExclude();		
		$nodir = array();
		if(0<count($exclude)){			
			foreach($files AS $i => $target){
				foreach($exclude AS $expression){
					if(preg_match($expression,$target)){
						unset($files[$i]);
						if(false===array_search(dirname($target),$nodir)){		
							$nodir[] = dirname($target);
						}
					}
				}
			}
		}
		
		$dirs = array();
		foreach($files AS $i => $target){
			$adapter->unlinkTargetFile($target);
			if(false===array_search(dirname($target),$dirs)){
				$dirs[] = dirname($target);
			}
		}
		
		foreach($dirs AS $dir){
			if(false===array_search($dir,$nodir)){
				if($adapter->isTargetDir($dir)){									
					$adapter->removeTargetDir($dir);
				}
			}
		}
	}
	
	/**
	 * @param AssetAdapterInterface $adapter
	 * @param string $path
	 * @return array
	 */
	protected function getRemoveFiles(AssetAdapterInterface $adapter,$path){
		$files = array();
		$filelist = $adapter->getTargetDirList($path);		
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$file = $path.'/'.$filename;
			if($adapter->isTargetDir($file)){
				$files = array_merge($files,$this->getRemoveFiles($adapter,$file));			
			} elseif($adapter->isTargetFile($file)){
				$files[] = $file;
			}
		}
		return $files;
	}
	
}