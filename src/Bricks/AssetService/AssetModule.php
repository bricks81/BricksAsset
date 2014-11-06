<?php

namespace Bricks\AssetService;

use Bricks\AssetService\ClassLoader\ClassLoaderInterface;

/**
 * Represents a module
 */
class AssetModule {
	
	protected $config = array();
	
	protected $moduleName;
	
	protected $classLoaderClass;
	
	/**
	 * @param array $config
	 * @param string $moduleName
	 * @param string $classLoader
	 */
	public function __construct(array $config,$moduleName){
		$this->setConfig($config);
		$this->setModuleName($moduleName);
	}
	
	/**
	 * @param array $config
	 */
	public function setConfig(array $config){
		$this->config = $config;
	}
	
	/**
	 * @return array
	 */
	public function getConfig(){
		return $this->config;
	}
	
	/**
	 * @param string $moduleName
	 */
	public function setModuleName($moduleName){
		$this->moduleName = $moduleName;
	}
	
	/**
	 * @return string
	 */
	public function getModuleName(){
		return $this->moduleName;
	}
	
	/**
	 * @return string
	 */
	public function getClassLoaderClass(){
		return $this->getConfig()['classLoader'];
	}
	
	/**
	 * @return ClassLoaderInterface
	 */
	public function getClassLoader(){		
		$class = $this->getClassLoaderClass();
		return $class::getInstance();
	}
	
	/**
	 * @return boolean
	 */
	public function isAutoPublish(){
		return $this->getConfig()['autoPublish']?true:false;
	}
	
	public function autoPublish(){
		if($this->isAutoPublish()){
			$this->publish();
		}		
	}
	
	/**
	 * @return boolean
	 */
	public function isAutoOptimize(){
		return $this->getConfig()['autoOptimize']?true:false;
	}
	
	public function autoOptimize(){		
		if($this->isAutoOptimize()){
			$this->optimize();
		}
	}
	
	/**
	 * @return \Bricks\AssetService\AssetServiceAwareInterface
	 */
	public function getAssetAdapter(){
		$class = $this->getConfig()['assetAdapter'];		
		return $this->getClassLoader()->get($class);
	}
	
	/**
	 * @return string
	 */
	public function getWwwrootPath(){
		return $this->getConfig()['wwwroot_path'];
	}
	
	/**
	 * @return string|false
	 */
	public function getHttpAssetsPath(){
		return $this->getConfig()['http_assets_path'];				
	}
	
	/**
	 * @return string
	 */
	public function getModuleAssetPath(){
		return $this->getConfig()['module_asset_path'];
	}
	
	public function remove(){
		$strategy = $this->getRemoveStrategy();
		$strategy->remove($this);
	}
	
	/**
	 * @throws \RuntimeException
	 * @return \Bricks\AssetService\RemoveStrategy\RemoveStrategyInterface
	 */
	public function getRemoveStrategy(){
		$strategy = $this->getConfig()['removeStrategy'];
		if(!class_exists($strategy,true)){
			throw new \RuntimeException('Strategy ('.$strategy.') not exists');
		}
		return $this->getClassLoader()->get($strategy);		
	}
	
	/**
	 * @throws \RuntimeException
	 * @return \Bricks\AssetService\PublishStrategy\PublishStrategyInterface
	 */
	public function getPublishStrategy(){
		$class = $this->getConfig()['publishStrategy'];
		if(!class_exists($class,true)){
			throw new \RuntimeException('Strategy ('.$class.') not exists');
		}
		return $this->getClassLoader()->get($class);		
	}
	
	public function publish(){
		$strategy = $this->getPublishStrategy();
		$strategy->publish($this);				
		if($this->hasLessSupport()){
			$strategy = $this->getLessStrategy();
			$strategy->less($this);
		}
		if($this->hasScssSupport()){
			$strategy = $this->getScssStrategy();
			$strategy->scss($this);
		}
	}
	
	/**
	 * @throws \RuntimeException
	 * @return \Bricks\AssetService\LessStrategy\LessStrategyInterface
	 */
	public function getLessStrategy(){
		$class = $this->getConfig()['lessStrategy'];
		if(!class_exists($class,true)){
			throw new \RuntimeException('Strategy ('.$class.') not exists');
		}
		return $this->getClassLoader()->get($class);
	}
	
	/**
	 * @return boolean
	 */
	public function hasLessSupport(){
		return $this->getConfig()['lessSupport']?true:false;
	}	
	
	/**
	 * @throws \RuntimeException
	 * @return \Bricks\AssetService\ScssStrategy\ScssStrategyInterface
	 */
	public function getScssStrategy(){
		$class = $this->getConfig()['scssStrategy'];
		if(!class_exists($class,true)){
			throw new \RuntimeException('Strategy ('.$class.') not exists');
		}
		return $this->getClassLoader()->get($class);
	}
	
	/**
	 * @return boolean
	 */
	public function hasScssSupport(){
		return $this->getConfig()['scssSupport']?true:false;
	}
	
	public function optimize(){
		if($this->hasMinifyCssSupport){
			$strategy = $this->getMinifyCssStrategy();
			$strategy->minify($this);
		}
		if($this->hasMinifyJsSupport){
			$strategy = $this->getMinifyJsStrategy();
			$strategy->minify($this);
		}
	}
	
	/**
	 * @throws \RuntimeException
	 * @return \Bricks\AssetService\MinifyCssStrategy\MinifyCssStrategyInterface
	 */
	public function getMinifyCssStrategy(){
		$class = $this->getConfig()['minifyCssStrategy'];
		if(!class_exists($class,true)){
			throw new \RuntimeException('Strategy ('.$class.') not exists');
		}
		return $this->getClassLoader()->get($class);
	}
	
	/**
	 * @return boolean
	 */
	public function hasMinifyCssSUpport(){
		return $this->getConfig()['minifyCssSupport']?true:false;
	}
	
	/**
	 * @throws \RuntimeException
	 * @return \Bricks\AssetService\MinifyJsStrategy\MinifyJsStrategyInterface
	 */
	public function getMinifyJsStrategy(){
		$class = $this->getConfig()['minifyJsStrategy'];
		if(!class_exists($class,true)){
			throw new \RuntimeException('Strategy ('.$class.') not exists');
		}
		return $this->getClassLoader()->get($class);
	}
	
	/**
	 * @return boolean
	 */
	public function hasMinifyJsSupport(){
		return $this->getConfig()['minifyJsSupport']?true:false;
	}
	
}