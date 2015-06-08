<?php
/**
 * Bricks Framework & Bricks CMS
 *
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Asset\MinifyJsStrategy;

use \JSMin;
use Bricks\Asset\Module;
use Bricks\Asset\StorageAdapter\StorageAdapterInterface;

class MrclayMinifyStrategy implements MinifyJsStrategyInterface {
	
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
	 * @see \Bricks\Asset\MinifyJsStrategy\MinifyStrategyInterface::minify()
	 */
	public function minify(){
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
		$update = $this->getMinifyUpdate($adapter,$path,$path);
		foreach($update AS $source => $target){
			$content = $adapter->readSourceFile($source);
			$content = JSMin::minify($content);
			$adapter->writeTargetFile($target,$content);
		}
	}
	
	/**
	 * @param StorageAdapterInterface $adapter
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getMinifyUpdate(StorageAdapterInterface $adapter,$source,$target){
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