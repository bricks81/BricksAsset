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
	 * @var string
	 */
	protected $assetModuleClass;
	
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
	 * @var string relative or absolute path to the www dir
	 */
	protected $wwwRootPath;
	
	/**
	 * @var string subpath after www dir
	 */
	protected $httpAssetsPath;
	
	/**
	 * @var string absolute path to the module assets dir
	 */
	protected $moduleAssetsPath;
	
	/**
	 * @param array $config
	 * @param array $loadedModules
	 */
	public function __construct(array $config,array $loadedModules){
		$this->loadedModules = $loadedModules;
		$defaults = array(
			'assetModule' => 'Bricks\AssetService\AssetModule',
			'autoPublish' => false,
			'autoOptimize' => false,
			'lessSupport' => false,
			'scssSupport' => false,
			'minifyCssSupport' => false,
			'minifyJsSupport' => false,
			'wwwRootPath' => null,
			'httpAssetsPath' => null,		
			'classLoader' => 'Bricks\AssetService\ClassLoader\ClassLoader',
			'assetAdapter' => 'Bricks\AssetService\AssetAdapter\FilesystemAdapter',
			'publishStrategy' => 'Bricks\AssetService\PublishStrategy\CopyStrategy',
			'removeStrategy' => 'Bricks\AssetService\RemoveStrategy\RemoveStrategy',
			'lessStrategy' => 'Bricks\AssetService\LessStrategy\NeilimeLessphpStrategy',
			'scssStrategy' => 'Bricks\AssetService\ScssStrategy\LeafoScssphpStrategy',
			'minifyCssStrategy' => 'Bricks\AssetService\MinifyCssStrategy\MrclayMinifyStrategy',
			'minifyJsStrategy' => 'Bricks\AssetService\MinifyJsStrategy\MrclayMinifyStrategy',
		);		
		
		$prepared = array();
		
		foreach($defaults AS $key => $default){
			if(isset($config[$key])){
				$prepared[$key] = $config[$key];
			} else {
				$prepared[$key] = $default;
			}
		}
		
		$solved = array();
		
		// fetch classloader
		foreach($prepared AS $key => $value){
			if('classLoader'==$key){
				$solved[$key] = $value::getInstance();				
			}
		}		
		
		// process each
		foreach($defaults AS $key => $default){
			if('classLoader'==$key){
				continue;
			}
			if(is_bool($default) || is_null($default) || 'assetModule' == $key){
				$var = $prepared[$key];				
			} else {
				$var = $solved['classLoader']->get($prepared[$key]);				
			}
			$solved[$key] = $var;			
		}
		
		// set the list
		foreach($solved AS $key => $var){
			if('assetModule'==$key){
				$this->setAssetModuleClass($var);
			} else {
				$this->{'set'.ucfirst($key)}($var);
			}
		}

		// setup modules
		$mConfig = $config['moduleSpecific'];
		foreach($this->loadedModules AS $moduleName){
			if(!isset($mConfig[$moduleName])){
				continue;
			}
			
			$classLoaderClass = isset($mConfig[$moduleName]['classLoader'])
				?$mConfig[$moduleName]['classLoader']
				:$this->getClassLoader();
			
			$assetModuleClass = isset($mConfig[$moduleName]['assetModule'])
				?$mConfig[$moduleName]['assetModule']
				:$this->getAssetModuleClass();
			
			if(is_object($classLoaderClass)){
				$classLoader = $classLoaderClass;
			} else {
				$classLoader = $classLoaderClass::getInstance();
			}
			$params = array(
				$prepared,
				$mConfig[$moduleName],
				$moduleName					
			);
			$module = $classLoader->get($assetModuleClass,$params);
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
	 * @param string $class
	 */
	public function setAssetModuleClass($class){
		$this->assetModuleClass = $class;
	}
	
	/**
	 * @return string
	 */
	public function getAssetModuleClass(){
		return $this->assetModuleClass;
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
	public function getAutoPublish(){
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
	public function getAutoOptimize(){
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
	public function getLessSupport(){
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
	public function getScssSupport(){
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
	public function getMinifyCssSupport(){
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
	public function getMinifyJsSupport(){
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
	 * @param string $path
	 */
	public function setWwwRootPath($path){
		$this->wwwRootPath = $path;
		$this->updateModules();
	}
	
	/**
	 * @return string
	 */
	public function getWwwRootPath(){
		return $this->wwwRootPath;
	}
	
	/**
	 * @param string $path
	 */
	public function setHttpAssetsPath($path){
		$this->httpAssetsPath = $path;
		$this->updateModules();
	}
	
	/**
	 * @return string
	 */
	public function getHttpAssetsPath(){
		return $this->httpAssetsPath;
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