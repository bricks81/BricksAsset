<?php

namespace Bricks\AssetService;

use Bricks\AssetService\ClassLoader\ClassLoaderInterface;
use Bricks\AssetService\AssetAdapter\AssetAdapterInterface;
use Bricks\AssetService\RemoveStrategy\RemoveStrategyInterface;
use Bricks\AssetService\PublishStrategy\PublishStrategyInterface;
use Bricks\AssetService\LessStrategy\LessStrategyInterface;
use Bricks\AssetService\ScssStrategy\ScssStrategyInterface;
use Bricks\AssetService\MinifyCssStrategy\MinifyCssStrategyInterface;
use Bricks\AssetService\MinifyJsStrategy\MinifyJsStrategyInterface;

/**
 * Represents a module
 */
class AssetModule {
	
	protected $config = array();
	
	protected $moduleName;
	
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
	 * @param string $moduleName
	 * @param string $classLoader
	 */
	public function __construct(array $config,$moduleName){
		$this->setModuleName($moduleName);
		$this->setConfig($config);		
	}
	
	/**
	 * @param array $config
	 */
	public function setConfig(array $config){
		$this->config = $config;
		$this->updateConfig();
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
	
	protected function updateConfig(){
		$cfg = $this->getConfig();		
		$classLoaderClass = $cfg['classLoader'];
		if(null==$this->getClassLoader()||!($this->getClassLoader() instanceof $classLoaderClass)){			
			$this->setClassLoader($classLoaderClass::getInstance());
		}
		$list = array(
			'assetAdapter',
			'publishStrategy',
			'removeStrategy',
			'lessStrategy',
			'scssStrategy',
			'minifyCssStrategy',
			'minifyJsStrategy',
		);
		foreach($list AS $part){
			${$part.'Class'} = $cfg['assetAdapter'];
			if(null==$this->{'get'.ucfirst($part)}()||!($this->{'get'.ucfirst($part)}() instanceof ${$part.'Class'})){			
				$this->{'set'.ucfirst($part)}($this->getClassLoader()->get(${$part.'Class'}));
			}
		}
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
		$this->config['classLoader'] = get_class($classLoader);
	}
	
	/**
	 * @return boolean
	 */
	public function isAutoPublish(){
		return $this->getConfig()['autoPublish']?true:false;
	}
	
	/**
	 * @param boolean $bool
	 */
	public function setIsAutoPublish($bool){
		$this->config['autoPublish'] = $bool?true:false;
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
	
	/**
	 * @param boolean $bool
	 */
	public function setIsAutoOptimize($bool){
		$this->config['autoOptimize'] = $bool?true:false;
	}
	
	public function autoOptimize(){		
		if($this->isAutoOptimize()){
			$this->optimize();
		}
	}
	
	/**
	 * @param AssetAdapterInterface $assetAdapter
	 */
	public function setAssetAdapter(AssetAdapterInterface $assetAdapter){
		$this->assetAdapter = $assetAdapter;
		$this->config['assetAdapter'] = get_class($assetAdapter);
	}
	
	/**
	 * @return \Bricks\AssetService\AssetServiceAwareInterface
	 */
	public function getAssetAdapter(){
		return $this->assetAdapter;
	}
	
	/**
	 * @return string
	 */
	public function getWwwrootPath(){
		return $this->getConfig()['wwwroot_path'];
	}
	
	/**
	 * @param string $path
	 */
	public function setWwwrootPath($path){
		$this->config['wwwroot_path'] = $path;
	}
	
	/**
	 * @return string|false
	 */
	public function getHttpAssetsPath(){
		return $this->getConfig()['http_assets_path'];				
	}
	
	/**
	 * @param string $relativePath relative to wwwroot_path
	 */
	public function setHttpAssetsPath($relativePath){
		$this->config['http_assets_path'] = $relativePath;
	}
	
	/**
	 * @return string
	 */
	public function getModuleAssetPath(){
		return $this->getConfig()['module_asset_path'];
	}
	
	/**
	 * @param string $absolutePath points to the modules directory
	 */
	public function setModuleAssetPath($absolutePath){
		$this->config['module_asset_path'] = $absolutePath;
	}
	
	public function remove(){
		$strategy = $this->getRemoveStrategy();
		$strategy->remove($this);
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
		$this->config['removeStrategy'] = get_class($removeStrategy);
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
		$this->config['publishStrategy'] = get_class($publishStrategy);
	}
	
	public function publish(){
		$strategy = $this->getPublishStrategy();
		$strategy->publish($this);				
		if($this->isLessSupport()){
			$strategy = $this->getLessStrategy();
			$strategy->less($this);
		}
		if($this->isScssSupport()){
			$strategy = $this->getScssStrategy();
			$strategy->scss($this);
		}
	}
	
	/**
	 * @return boolean
	 */
	public function isLessSupport(){
		return $this->getConfig()['lessSupport']?true:false;
	}
	
	/**
	 * @param boolean $bool
	 */
	public function setIsLessSupport($bool){
		$this->config['lessSupport'] = $bool?true:false;
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
		$this->config['lessStrategy'] = get_class($lessStrategy);
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
		$this->config['scssStrategy'] = get_class($scssStrategy);
	}
	
	/**
	 * @return boolean
	 */
	public function isScssSupport(){
		return $this->getConfig()['scssSupport']?true:false;
	}
	
	/**
	 * @param boolean $bool
	 */
	public function setIsScssSupport($bool){
		$this->config['scssSupport'] = $bool?true:false;
	}
	
	public function optimize(){
		if($this->isMinifyCssSupport()){
			$strategy = $this->getMinifyCssStrategy();
			$strategy->minify($this);
		}
		if($this->isMinifyJsSupport()){
			$strategy = $this->getMinifyJsStrategy();
			$strategy->minify($this);
		}
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
		$this->config['minifyCssStrategy'] = get_class($minifyCssStrategy);
	}
	
	/**
	 * @return boolean
	 */
	public function isMinifyCssSupport(){
		return $this->getConfig()['minifyCssSupport']?true:false;
	}
	
	/**
	 * @param boolean $bool
	 */
	public function setIsMinifyCssSupport($bool){
		$this->config['minifyCssSupport'] = $bool?true:false;
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
		$this->config['minifyJsStrategy'] = get_class($minifyJsStrategy);
	}
	
	/**
	 * @return boolean
	 */
	public function isMinifyJsSupport(){
		return $this->getConfig()['minifyJsSupport']?true:false;
	}
	
	/**
	 * @param boolean $bool
	 */
	public function setIsMinifyJsSupport($bool){
		$this->config['minifyJsSUpport'] = $bool?true:false;
	}
	
}