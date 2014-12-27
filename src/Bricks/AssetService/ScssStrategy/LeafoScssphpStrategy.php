<?php

namespace Bricks\AssetService\ScssStrategy;

use Leafo\ScssPhp\Compiler;
use Bricks\AssetService\AssetModule;
use Bricks\AssetService\AssetAdapter\AssetAdapterInterface;
use Bricks\AssetService\ScssStrategy\ScssStrategyInterface;

class LeafoScssphpStrategy implements ScssStrategyInterface {

	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\ScssStrategy\ScssStrategyInterface::scss()
	 */
	public function scss(AssetModule $module){
		$adapter = $module->getAssetAdapter();
		$path = realpath($module->getWwwRootPath().'/'.$module->getHttpAssetsPath().'/'.$module->getModuleName());
		if(false==$path){
			return;
		}
		
		$scss = new Compiler();
		
		$imports = $this->getScssImports($adapter,$path);
		$update = $this->getScssUpdate($adapter,$path,$path);
		
		foreach($imports AS $s => $files){
			foreach($files AS $f){
				$scss->addImportPath(dirname($f));
				if(isset($update[$f])){
					unset($update[$f]);
				}
			}
		}
		
		foreach($update AS $source => $target){
			$content = $adapter->readSourceFile($source);
			$content = $scss->compile($content);
			$adapter->writeTargetFile($target,$content);
		}
	}
	
	protected function getScssImports(AssetAdapterInterface $adapter,$source){
		$imports = array();
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]||'_'==$filename[0]){
				continue;
			}
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$imports += $this->getScssImports($adapter,$_source);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-5)!='.scss'&&substr($_source,-5)!='.sass'){
					continue;
				}
				if(substr($_source,-9)=='.scss.css'||substr($_source,-9)=='.sass.css'){
					continue;
				}
				$content = $adapter->readSourceFile($_source);
				if(preg_match_all('#@import(.*?)\n#i',$content,$matches)){					
					foreach($matches[1] AS $string){
						$imports[$_source][] = dirname($_source).'/_'.trim($string,' ";\'').'.scss';
						$imports[$_source][] = dirname($_source).'/_'.trim($string,' ";\'').'.sass';
					}
				}
			}
		}
		return $imports;
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
			if('.'==$filename[0]||'_'==$filename[0]){
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
				//if(!$adapter->isTargetFile($_target)||$adapter->isOlderThan($_source,$_target)){
					$update[$_source] = $_target;
				//}
			}
		}
		return $update;
	}
	
}