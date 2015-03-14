<?php

namespace Bricks\Asset\LessStrategy;

use \lessc;
use Bricks\Asset\AssetModule;
use Bricks\Asset\AssetAdapter\AssetAdapterInterface;
use Bricks\Asset\LessStrategy\LessStrategyInterface;

class OyejorgeLessphpStrategy implements LessStrategyInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\LessStrategy\LessStrategyInterface::less()
	 */
	public function less(AssetModule $module){
		$adapter = $module->getAssetAdapter();
		$path = realpath($module->getWwwRootPath().'/'.$module->getHttpAssetsPath().'/'.$module->getModuleName());
		if(false==$path){
			return;
		}
		$lessc = new lessc();
		
		$imports = $this->getLessImports($adapter,$path);
		$update = $this->getLessUpdate($adapter,$path,$path);
		foreach($imports AS $s => $files){
			foreach($files AS $f){
				$lessc->addImportDir(dirname($f));
				if(isset($update[$f])){
					unset($update[$f]);
				}
			}			
		}				
		
		foreach($update AS $source => $target){
			$content = $adapter->readSourceFile($source);
			$content = $lessc->compile($content);
			$adapter->writeTargetFile($target,$content);
		}		
	}
	
	protected function getLessImports(AssetAdapterInterface $adapter,$source){
		$imports = array();
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$imports += $this->getLessImports($adapter,$_source);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-5)!='.less'||substr($_source,-9)=='.less.css'){
					continue;
				}
				$content = $adapter->readSourceFile($_source);
				if(preg_match_all('#@import(.*?)\n#i',$content,$matches)){
					foreach($matches[1] AS $string){
						$imports[$_source][] = dirname($_source).'/'.trim($string,' ";\'');
					}					
				}
			}			
		}
		return $imports;
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
				//if(!$adapter->isTargetFile($_target)||$adapter->isOlderThan($_source,$_target)){
					$update[$_source] = $_target;
				//}
			}
		}
		return $update;
	}
	
	
}