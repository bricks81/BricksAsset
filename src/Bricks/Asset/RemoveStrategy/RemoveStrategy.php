<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *  
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Asset\RemoveStrategy;

use Bricks\Asset\Module;
use Bricks\Asset\RemoveStrategy\RemoveStrategyInterface;
use Bricks\Asset\StorageAdapter\StorageAdapterInterface;

class RemoveStrategy implements RemoveStrategyInterface {
	
	/**
	 * @var Module
	 */
	protected $module;
	
	/**
	 * @var \Bricks\Asset\StorageAdapter\StorageAdapterInterface
	 */
	protected $storageAdapter;
	
	/**
	 * @param Module $module
	 */
	public function __construct(Module $module){
		$this->setModule($module);
	}
	
	/**
	 * @param Module $module
	 */
	public function setModule(Module $module){
		$this->module = $module;
	}
	
	/**
	 * @return \Bricks\Asset\Module
	 */
	public function getModule(){
		return $this->module;
	}
	
	/**
	 * @param StorageAdapterInterface $adapter
	 */
	public function setStorageAdapter(StorageAdapterInterface $adapter){
		$this->storageAdapter = $adapter;
	}
	
	/**
	 * @return \Bricks\Asset\StorageAdapter\StorageAdapterInterface
	 */
	public function getStorageAdapter(){
		return $this->storageAdapter;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\RemoveStrategy\RemoveStrategyInterface::remove()
	 */
	public function remove($moduleName){
		$adapter = $this->getStorageAdapter();
		$module = $this->getModule();
		$moduleName = $module->getModuleName();
		$asset = $module->getAsset();
		$config = $asset->getConfig()->getArray($moduleName);
		
		$path = realpath(
			$config['wwwRootPath'].'/'.
			$config['httpAssetsPath'].'/'.
			$moduleName
		);
		if(false==$path){
			return;
		}
		
		$files = $this->getRemoveFiles($adapter,$path);		
		$exclude = $config['removeExclude'];		
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
	 * @param StorageAdapterInterface $adapter
	 * @param string $path
	 * @return array
	 */
	protected function getRemoveFiles(StorageAdapterInterface $adapter,$path){
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