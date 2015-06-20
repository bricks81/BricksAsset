<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *  
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Asset\PublishStrategy;

use Bricks\Asset\PublishStrategy\PublishStrategyInterface;
use Bricks\Asset\StorageAdapter\StorageAdapterInterface;
use Bricks\Asset\Module;

class CopyStrategy implements PublishStrategyInterface {
	
	/**
	 * @var \Bricks\Asset\Module
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
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\PublishStrategy\PublishStrategyInterface::publish()
	 */
	public function publish(){		
		$adapter = $this->getModule()->getStorageAdapter();
		$module = $this->getModule();
		$moduleName = $module->getModuleName();
		$asset = $module->getAsset();
		$config = $asset->getConfig()->getArray($moduleName);
		
		$modulePath = $config['moduleAssetsPath'];
		$root = realpath($config['wwwRootPath']);
		$path = $root.'/'.$config['httpAssetsPath'].'/'.$moduleName;
		if(false==$modulePath||false==$root){			
			return;
		}
		$update = $this->getPublishUpdate($adapter,$modulePath,$path);
		
		// exclude patterns
		$exclude = $config['publishExclude'];
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
	 * @param StorageAdapterInterface $adapter
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getPublishUpdate(StorageAdapterInterface $adapter,$source,$target){
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