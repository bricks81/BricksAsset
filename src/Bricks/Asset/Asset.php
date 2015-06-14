<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *  
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Asset;

use Bricks\Config\ConfigInterface;
use Bricks\ClassLoader\ClassLoaderInterface;

class Asset {
	
	/**
	 * an array containing strings with the names of each module
	 * @var array
	 */
	protected $loadedModules = array();
	
	/**
	 * @var \Bricks\Config\ConfigInterface
	 */
	protected $config = array();
	
	/**
	 * @var \Bricks\ClassLoader\ClassLoaderInterface
	 */
	protected $classLoader;
	
	/**
	 *  @var array
	 */
	protected $assets = array();
	
	/**
	 * @param ConfigInterface $config
	 * @param ClassLoaderInterface $classLoader
	 * @param array $loadedModules
	 */
	public function __construct(
		ConfigInterface $config,
		ClassLoaderInterface $classLoader,
		array $loadedModules=array()
	){		
		$this->loadedModules = $loadedModules;
		$this->config = $config;
		$this->classLoader = $classLoader;				
	}	
	
	/**
	 * @param ConfigInterface $config
	 */
	public function setConfig(ConfigInterface $config){
		$this->config = $config;
	}
	
	/**
	 * @return \Bricks\Config\ConfigInterface
	 */
	public function getConfig(){
		return $this->config;
	}
	
	/**
	 * @param ClassLoaderInterface $classLoader
	 */
	public function setClassLoader(ClassLoaderInterface $classLoader){
		$this->classLoader = $classLoader;
	}
	
	/**
	 * @return \Bricks\ClassLoader\ClassLoaderInterface
	 */
	public function getClassLoader(){
		return $this->classLoader;
	}
	
	/**
	 * @param array $loadedModules
	 */
	public function setLoadedModules(array $loadedModules=array()){
		$this->loadedModules = $loadedModules;
	}
	
	/**
	 * @return array
	 */
	public function getLoadedModules(){
		return $this->loadedModules;
	}
	
	/**
	 * @param string $moduleName
	 */
	public function hasAsset($moduleName){
		return isset($this->assets[$moduleName]);
	}
	
	/**
	 * @param string $moduleName
	 * @param Module $module
	 */
	public function setAsset($moduleName,Module $module){
		$this->assets[$moduleName] = $module;
	}
	
	/**
	 * @param string $moduleName
	 * @return Module
	 */
	public function getAsset($moduleName){
		if(!$this->hasAsset($moduleName)){						
			$module = $this->getClassLoader()->newInstance(__CLASS__,__FUNCTION__,'assetModule',$moduleName,array(
				'Asset' => $this,
				'moduleName' => $moduleName				
			));			
			$this->setAsset($moduleName,$module);
		}
		return $this->assets[$moduleName];
	}
	
	/**
	 * @param array $modules
	 */
	public function autoPublish(array $modules=array()){
		if(0==count($modules)){
			$modules = $this->getLoadedModules();
		}
		foreach($modules AS $moduleName){
			$module = $this->getAsset($moduleName);
			if($this->getConfig()->get('isAutoPublish',$moduleName)){
				$module->publish();
			}
		}
	}
	
	/**
	 * @param array $modules
	 */
	public function autoOptimize(array $modules=array()){
		if(0==count($modules)){
			$modules = $this->getLoadedModules();
		}
		foreach($modules AS $moduleName){
			$module = $this->getAsset($moduleName);
			if($this->getConfig()->get('isAutoOptimize',$moduleName)){
				$module->optimize();
			}
		}
	}
	
}
