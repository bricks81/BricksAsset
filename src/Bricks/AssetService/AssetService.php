<?php

namespace Bricks\AssetService;

use Bricks\AssetService\ClassLoader\ClassLoaderInterface;
use Bricks\AssetService\AssetAdapter\AssetAdapterInterface;
use Bricks\AssetService\PublishStrategy\PublishStrategyInterface;
use Bricks\AssetService\RemoveStrategy\RemoveStrategyInterface;
use Bricks\AssetService\LessStrategy\LessStrategyInterface;
use Bricks\AssetService\ScssStrategy\ScssStrategyInterface;
use Bricks\AssetService\MinifyCssStrategy\MinifyCssStrategyInterface;
use Bricks\AssetService\MinifyJsStrategy\MinifyJsStrategyInterface;
/**
 * Service which allow us to control each module
 */
class AssetService {
	
	/**
	 * an array containing strings with the names of each module
	 * @var array
	 */
	protected $loadedModules = array();
	
	/**
	 * Contains each setted up module
	 * @var array
	 */
	protected $modules = array();
	
	protected $autoPublish = false;
	
	protected $autoOptimize = false;
	
	protected $lessSupport = false;
	
	protected $scssSupport = false;
	
	protected $minifyCssSupport = false;
	
	protected $minifyJsSupport = false;
	
	/**
	 * @var \Bricks\AssetService\ClassLoader\ClassLoaderInterface
	 */
	protected $classLoader;
	
	/**
	 * @var \Bricks\AssetService\AssetAdapter\AssetAdapterInterface
	 */
	protected $assetAdapter;
	
	/**
	 * @var \Bricks\AssetService\PublishStrategy\PublishStrategyInterface
	 */
	protected $publishStrategy;
	
	/**
	 * @var \Bricks\AssetService\RemoveStrategy\RemoveStrategyInterface
	 */
	protected $removeStrategy;
	
	/**
	 * @var \Bricks\AssetService\LessStrategy\LessStrategyInterface
	 */
	protected $lessStrategy;
	
	/**
	 * @var \Bricks\AssetService\ScssStrategy\ScssStrategyInterface
	 */
	protected $scssStrategy;
	
	/**
	 * @var \Bricks\AssetService\MinifyCssStrategy\MinifyCssStrategyInterface
	 */
	protected $minifyCssStrategy;
	
	/**
	 * @var \Bricks\AssetService\MinifyJsStrategy\MinifyJsStrategyInterface
	 */
	protected $minifyJsStrategy;
	
	/**
	 * @param array $config
	 * @param array $loadedModules
	 */
	public function __construct(array $config,array $loadedModules){
		$this->loadedModules = $loadedModules;
		$list = array(
			'autoPublish' => false,
			'autoOptimize' => false,
			'lessSupport' => false,
			'scssSupport' => false,
			'minifyCssSupport' => false,
			'minifyJsSupport' => false,
			'wwwRootPath' => null,
			'httpAssetsPath' => null,
			'moduleAssetsPath' => null,
			'classLoader' => 'Bricks\AssetService\ClassLoader\ClassLoader',
			'assetAdapter' => 'Bricks\AssetService\AssetAdapter\FilesystemAdapter',
			'lessStrategy' => 'Bricks\AssetService\LessStrategy\NeilimeLessphpStrategy',
			'scssStrategy' => 'Bricks\AssetService\ScssStrategy\LeafoScssphpStrategy',
			'minifyCssStrategy' => 'Bricks\AssetService\MinifyCssStrategy\MrclayMinifyStrategy',
			'minifyJsStrategy' => 'Bricks\AssetService\MinifyJsStrategy\MrclayMinifyStrategy',
		);
		foreach($list AS $key => $default){
			if('classLoader'==$key){
				if(!isset($config[$key])){
					$object = $default::getInstance();
				} else {
					$object = $config[$key]::getInstance();
				}
				$classLoader = $object;
			} else {
				if(is_bool($default)){
					if(!isset($config[$key])){
						$var = $default;
					} else {
						$var = $config[$key];
					}
				} elseif(is_null($default)){
					if(!isset($config[$key])){
						$var = $default;
					} else {
						$var = $config[$key];
					}
				} else {
					if(!isset($config[$key])){
						$var = $classLoader->get($default);
					} else {
						$var = $classLoader->get($config[$key]);
					}
				}
			}
			$this->$key = $var;
		}

		// setup modules
		$config = $config['moduleSpecific'];
		foreach($this->getLoadedModules AS $moduleName){
			if(!isset($config[$moduleName])){
				continue;
			}
			
			$classLoaderClass = isset($config[$moduleName]['classLoader'])
				?$config[$moduleName]['classLoader']
				:$cfg['classLoader'];
			
			$assetModuleClass = isset($config[$moduleName]['assetModule'])
				?$config[$moduleName]['assetModule']
				:$cfg['assetModule'];
			
			if(!count($this->modules)){
				$classLoader = $classLoaderClass::getInstance();
				$module = $classLoader->get($assetModuleClass,array($config[$moduleName],$moduleName));
			}
			$this->setModule($module);
			
		}
				
	}
	
	
	
	/**
	 * @return array
	 */
	public function getLoadedModules(){
		return $this->loadedModules;
	}
	
	/**
	 * @param AssetModule $module
	 */
	public function setModule(AssetModule $module){
		$this->modules[$module->getModuleName()] = $module;
	}
	
	/**
	 * @param string $moduleName
	 * @return AssetModule
	 */
	public function getModule($moduleName){
		return $this->modules[$moduleName];
	}
	
	/**
	 * @return array
	 */
	public function getModules(){		
		return $this->modules;
	}
	
	/**
	 * @param string $moduleName
	 * @return boolean
	 */
	public function hasModule($moduleName){
		return isset($this->modules[$moduleName]);
	}
	
	/**
	 * @return boolean
	 */
	public function isAutoPublish(){
		return $this->autoPublish;
	}
	
	/**
	 * @param boolean $bool
	 */
	public function setAutoPublish($bool){
		$this->autoPublish = $bool?true:false;
		$this->updateModules();		
	}
	
	/**
	 * Publish if it's configured as publish automaticly
	 */
	public function autoPublish(){
		foreach($this->getModules() AS $module){
			$module->autoPublish();
		}
	}
	
	/**
	 * @return boolean
	 */
	public function isAutoOptimize(){
		return $this->autoOptimize;
	}
	
	/**
	 * @param boolean $bool
	 */
	public function setAutoOptimize($bool){
		$this->autoOptimize = $bool?true:false;
		$this->updateModules();
	}
	
	/**
	 * Optimize if it's configured as optimize automaticly
	 */
	public function autoOptimize(){
		foreach($this->getModules() AS $module){
			$module->autoPublish();
		}
	}
	
	/**
	 * @return boolean
	 */
	public function isLessSupport(){
		return $this->lessSupport;
	}
	
	/**
	 * @param boolean $bool
	 */
	public function setLessSupport($bool){
		$this->lessSupport = $bool?true:false;
		$this->updateModules();
	}
	
	/**
	 * @return boolean
	 */
	public function isScssSupport(){
		return $this->scssSupport;
	}
	
	/**
	 * @param boolean $bool
	 */
	public function setScssSupport($bool){
		$this->scssSupport = $bool?true:false;
		$this->updateModules();
	}
	
	/**
	 * @return boolean
	 */
	public function isMinifyCssSupport(){
		return $this->minifyCssSupport;
	}
	
	/**
	 * @param boolean $bool
	 */
	public function setMinifyCssSupport($bool){
		$this->minifyCssSupport = $bool?true:false;
		$this->updateModules();
	}
	
	/**
	 * @return boolean
	 */
	public function isMinifyJsSupport(){
		return $this->minifyJsSupport;
	}
	
	/**
	 * @param boolean $bool
	 */
	public function setMinifyJsSupport($bool){
		$this->minifyJsSupport = $bool?true:false;
		$this->updateModules();
	}
	
	/**
	 * @return \Bricks\AssetService\ClassLoader\ClassLoaderInterface
	 */
	public function getClassLoader(){
		return $this->classLoader;
	}
	
	/**
	 * @param ClassLoaderInterface $classLoader
	 */
	public function setClassLoader(ClassLoaderInterface $classLoader){
		$this->classLoader = $classLoader;
		$this->updateModules();
	}
	
	/**
	 * @return \Bricks\AssetService\AssetAdapter\AssetAdapterInterface
	 */
	public function getAssetAdapter(){
		return $this->assetAdapter;
	}
	
	/**
	 * @param AssetAdapterInterface $assetAdapter
	 */
	public function setAssetAdapter(AssetAdapterInterface $assetAdapter){
		$this->assetAdapter = $assetAdapter;
		$this->updateModules();
	}
	
	/**
	 * @return \Bricks\AssetService\PublishStrategy\PublishStrategyInterface
	 */
	public function getPublishStrategy(){
		return $this->publishStrategy;
	}
	
	/**
	 * @param PublishStrategyInterface $publishStrategy
	 */
	public function setPublishStrategy(PublishStrategyInterface $publishStrategy){
		$this->publishStrategy = $publishStrategy;
		$this->updateModules();
	}
	
	/**
	 * @return \Bricks\AssetService\RemoveStrategy\RemoveStrategyInterface
	 */
	public function getRemoveStrategy(){
		return $this->removeStrategy;
	}
	
	/**
	 * @param RemoveStrategyInterface $removeStrategy
	 */
	public function setRemoveStrategy(RemoveStrategyInterface $removeStrategy){
		$this->removeStrategy = $removeStrategy;
		$this->updateModules();
	}
	
	/**
	 * @return \Bricks\AssetService\LessStrategy\LessStrategyInterface
	 */
	public function getLessStrategy(){
		return $this->lessStrategy;
	}
	
	/**
	 * @param LessStrategyInterface $lessStrategy
	 */
	public function setLessStrategy(LessStrategyInterface $lessStrategy){
		$this->lessStrategy = $lessStrategy;
		$this->updateModules();
	}
	
	/**
	 * @return \Bricks\AssetService\ScssStrategy\ScssStrategyInterface
	 */
	public function getScssStrategy(){
		return $this->scssStrategy;
	}
	
	/**
	 * @param ScssStrategyInterface $scssStrategy
	 */
	public function setScssStrategy(ScssStrategyInterface $scssStrategy){
		$this->scssStrategy = $scssStrategy;
		$this->updateModules();
	}
	
	/**
	 * @return \Bricks\AssetService\MinifyCssStrategy\MinifyCssStrategyInterface
	 */
	public function getMinifyCssStrategy(){
		return $this->minifyCssStrategy;
	}
	
	/**
	 * @param MinifyCssStrategyInterface $minifyCssStrategy
	 */
	public function setMinifyCssStrategy(MinifyCssStrategyInterface $minifyCssStrategy){
		$this->minifyCssStrategy = $minifyCssStrategy;
		$this->updateModules();
	}
	
	/**
	 * @return \Bricks\AssetService\MinifyJsStrategy\MinifyJsStrategyInterface
	 */
	public function getMinifyJsStrategy(){
		return $this->minifyJsStrategy;
	}
	
	/**
	 * @param MinifyJsStrategyInterface $minifyJsStrategy
	 */
	public function setMinifyJsStrategy(MinifyJsStrategyInterface $minifyJsStrategy){
		$this->minifyJsStrategy = $minifyJsStrategy;
		$this->updateModules();
	}
	
	/**
	 * Give the asset service to the modules if a default value will be changed
	 */
	public function updateModules(){
		foreach($this->modules AS $module){
			$module->defaultsChanged($this);
		}
	}
	
	/**
	 * Removes the modules publish files from the http assets path
	 * @param string|array $modules module name(s)
	 */
	public function remove($modules=array()){
		if(is_string($modules)){
			$modules = array($modules);
		}
		if(!count($modules)){
			$modules = $this->getLoadedModules();
		}		
		foreach($modules AS $moduleName){
			$module = $this->getModule($moduleName);
			$module->remove();			
		}
	}
	
	/**
	 * Publish to the http assets path
	 * @param array|string $modules module name(s)
	 */
	public function publish($modules=array()){
		if(is_string($modules)){
			$modules = array($modules);
		}
		if(!count($modules)){
			$modules = $this->getLoadedModules();
		}
		foreach($modules AS $moduleName){
			$module = $this->getModule($moduleName);
			$module->publish();			
		}
		
	}
	
	/**
	 * Optimize alle files in http assets path
	 * @param array|string $module module name(s)
	 */
	public function optimize($modules=array()){
		if(is_string($modules)){
			$modules = array($modules);
		}
		if(!count($modules)){
			$modules = $this->getLoadedModules();
		}		
		foreach($modules AS $moduleName){
			$module = $this->getModule($moduleName);
			$module->optimize();													
		}
		
	}
	
}