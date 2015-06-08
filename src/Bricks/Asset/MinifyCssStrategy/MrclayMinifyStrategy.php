<?php
/**
 * Bricks Framework & Bricks CMS
 *
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Asset\MinifyCssStrategy;

use \CSSmin;
use Bricks\Asset\MinifyCssStrategy\MinifyCssStrategyInterface;
use Bricks\Asset\StorageAdapter\StorageAdapterInterface;
use Bricks\Asset\Module;

class MrclayMinifyStrategy implements MinifyCssStrategyInterface {
	
	/**
	 * @var \Bricks\Asset\Module
	 */
	protected $module;
	
	/**
	 * @var \Bricks\Asset\StorageAdapter\StorageAdapterInterface
	 */
	protected $storageAdapter;
	
	/**
	 * @var \CSSmin
	 */
	protected $minify;
	
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
		if(!$this->storageAdapter){
			$module = $this->getModule();
			$moduleName = $module->getModuleName();
			$asset = $module->getAsset();
			$classLoader = $asset->getClassLoader();
			$this->storageAdapter = $classLoader->newInstance(__CLASS__,__METHOD__,'storageAdapterClass',$moduleName,array(
				'Asset' => $asset
			));			
		}
		return $this->storageAdapter;
	}
	
	/**
	 * @param CSSmin $minify
	 */
	public function setMinify(CSSmin $minify){
		$this->minify = $minify;
	}
	
	/**
	 * @return CSSmin
	 */
	public function getMinify(){
		if(!$this->minify){
			$this->minify = new CSSmin();
		}
		return $this->minify;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\MinifyCssStrategy\MinifyStrategyInterface::minify()
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
			$min = $this->getMinify();
			$content = $min->run($content);
			$adapter->writeTargetFile($target,$content);	
		}
	}
	
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