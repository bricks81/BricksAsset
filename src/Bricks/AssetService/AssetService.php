<?php

namespace Bricks\AssetService;

/**
 * Service which allow us to control each module
 */
class AssetService {
	
	protected $config = array();
	
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
	
	/**
	 * @param array $config
	 * @param array $loadedModules
	 */
	public function __construct(array $config,array $loadedModules){
		$this->setLoadedModules($loadedModules);
		$this->setConfig($config);								
	}
	
	/**
	 * @param array $config
	 */
	public function setConfig(array $config){		
		$this->config = $config;		
		$this->updateModules();		
	}
	
	/**
	 * @return array
	 */
	public function getConfig(){	
		return $this->config;
	}
	
	/**
	 * @param array $loadedModules
	 */
	public function setLoadedModules(array $loadedModules){
		$this->loadedModules = $loadedModules;
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
	 * re-/configures setted up modules
	 */
	protected function updateModules(){
		$cfg = $this->getConfig();		
		foreach($this->getLoadedModules() AS $moduleName){
			if(!isset($cfg['module_specific'][$moduleName])){
				continue;
			}
			
			$classLoaderClass = isset($cfg['module_specific'][$moduleName]['classLoader'])
				?$cfg['module_specific'][$moduleName]['classLoader']
				:$cfg['classLoader'];							
			
			$assetModuleClass = isset($cfg['module_specific'][$moduleName]['assetModule'])
				?$cfg['module_specific'][$moduleName]['assetModule']
				:$cfg['assetModule'];
			
			$config = array();
			foreach($cfg AS $key => $value){
				if('module_specific'==$key){
					continue;
				}
				$config[$key] = $value;
			}
			$config = array_merge($config,$cfg['module_specific'][$moduleName]);
			
			if(!count($this->modules)){
				$classLoader = $classLoaderClass::getInstance();
				$module = $classLoader->get($assetModuleClass,array($config,$moduleName));					
			} else {
				$module = $this->getModule($moduleName);				
				$module->setConfig($config);
			}
			$this->setModule($module);
		}		
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
	 * Optimize if it's configured as optimize automaticly
	 */
	public function autoOptimize(){
		foreach($this->getModules() AS $module){
			$module->autoPublish();
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