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
 * 
 * The parameter $isDefault on the setter methods will be used
 * when the defaults on the asset service will be changed.
 * If it's true each change on the asset service
 * will change this specific value in the module, too.
 */
class AssetModule {
	
	protected $moduleName;
	
	protected $isAutoPublish = false;
	
	protected $isAutoOptimize = false;
	
	protected $lessSupport = false;
	
	protected $scssSupport = false;
	
	protected $minifyCssSupport = false;
	
	protected $minifyJsSupport = false;
	
	protected $wwwRootPath;
	
	protected $httpAssetsPath;
	
	protected $moduleAssetsPath;
	
	protected $isDefault = array();
	
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
	 */
	public function __construct(array $config,$moduleName){
		$this->moduleName = $moduleName;
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
					$this->isDefault[$key] = true;
				} else {
					$object = $config[$key]::getInstance();
					$this->isDefault[$key] = false;
				}
				$classLoader = $object;
			} else {
				if(is_bool($default)){
					if(!isset($config[$key])){
						$var = $default;
						$isDefault = true;						
					} else {
						$var = $config[$key];
						$isDefault = false;
					}
				} elseif(is_null($default)){
					if(!isset($config[$key])){
						$var = $default;
						$isDefault = true;
					} else {
						$var = $config[$key];
						$isDefault = false;
					}
				} else {
					if(!isset($config[$key])){
						$var = $classLoader->get($default);
						$isDefault = true;
					} else {
						$var = $classLoader->get($config[$key]);
						$isDefault = false;
					}
				}
			}
			$this->{'set'.ucfirst($key)}($var,$isDefault);
		}
	}
	
	/**
	 * @return string
	 */
	public function getModuleName(){
		return $this->moduleName;
	}
	
	/**
	 * @return \Bricks\AssetService\ClassLoader\ClassLoaderInterface
	 */
	public function getClassLoader(){		
		return $this->classLoader;
	}
	
	/**
	 * @param ClassLoaderInterface $classLoader
	 * @param boolean $isDefault	 
	 */
	public function setClassLoader(ClassLoaderInterface $classLoader,$isDefault=false){
		$this->classLoader = $classLoader;
		$this->isDefault['classLoader'] = $isDefault?true:false;	
	}
	
	/**
	 * @return boolean
	 */
	public function isAutoPublish(){
		return $this->isAutoPublish;
	}
	
	/**
	 * @param boolean $bool
	 * @param boolean $isDefault
	 */
	public function setAutoPublish($bool,$isDefault=false){
		$this->isAutoPublish = $bool?true:false;		
		$this->isDefault['autoPublish'] = $isDefault?true:false;
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
		return $this->isAutoOptimize;
	}
	
	/**
	 * @param boolean $bool
	 * @param boolean $isDefault
	 */
	public function setAutoOptimize($bool,$isDefault=false){
		$this->isAutoOptimize = $bool?true:false;
		$this->isDefault['autoOptimize'] = $isDefault?true:false;
	}
	
	public function autoOptimize(){		
		if($this->isAutoOptimize()){
			$this->optimize();
		}
	}
	
	/**
	 * @param AssetAdapterInterface $assetAdapter
	 * @param boolean $isDefault
	 */
	public function setAssetAdapter(AssetAdapterInterface $assetAdapter,$isDefault=false){
		$this->assetAdapter = $assetAdapter;
		$this->isDefault['assetAdapter'] = $isDefault?true:false;
	}
	
	/**
	 * @return \Bricks\AssetService\AssetServiceAwareInterface
	 */
	public function getAssetAdapter(){
		return $this->assetAdapter;
	}
	
	/**
	 * @return string|null
	 */
	public function getWwwRootPath(){
		return $this->wwwRootPath;
	}

	/**
	 * @param string $path absolute or relative path to the www reachable directory
	 * @param boolean $isDefault
	 */
	public function setWwwRootPath($path,$isDefault=false){
		$this->wwwRootPath = $path;
		$this->isDefault['wwwRootPath'] = $isDefault?true:false;
	}
	
	/**
	 * @return string|null
	 */
	public function getHttpAssetsPath(){
		return $this->httpAssetsPath;				
	}
	
	/**
	 * @param string $relativePath relative to $wwwRootPath
	 * @param boolean $isDefault
	 */
	public function setHttpAssetsPath($relativePath,$isDefault=false){
		$this->httpAssetsPath = $relativePath;
		$this->isDefault['httpAssetsPath'] = $isDefault?true:false;
	}
	
	/**
	 * @return string|null
	 */
	public function getModuleAssetsPath(){
		return $this->moduleAssetsPath;
	}
	
	/**
	 * @param string $absolutePath points to the modules directory
	 * @param boolean $isDefault
	 */
	public function setModuleAssetsPath($absolutePath,$isDefault=false){
		$this->moduleAssetsPath;
		$this->isDefault['moduleAssetsPath'] = $isDefault?true:false;
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
	 * @param boolean $isDefault
	 */
	public function setRemoveStrategy(RemoveStrategyInterface $removeStrategy,$isDefault=false){
		$this->removeStrategy = $removeStrategy;				
		$this->isDefault['removeStrategy'] = $isDefault?true:false;
	}
	
	/**
	 * @return \Bricks\AssetService\PublishStrategy\PublishStrategyInterface
	 */
	public function getPublishStrategy(){
		return $this->publishStrategy;		
	}

	/**
	 * @param PublishStrategyInterface $publishStrategy
	 * @param boolean $isDefault
	 */
	public function setPublishStrategy(PublishStrategyInterface $publishStrategy,$isDefault=false){
		$this->publishStrategy = $publishStrategy;		
		$this->isDefault['publishStrategy'] = $isDefault?true:false;
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
		return $this->lessSupport;
	}
	
	/**
	 * @param boolean $bool
	 * @param boolean $isDefault 
	 */
	public function setLessSupport($bool,$isDefault=false){
		$this->lessSupport = $bool?true:false;
		$this->isDefault = $isDefault?true:false;
	}
	
	/**
	 * @return \Bricks\AssetService\LessStrategy\LessStrategyInterface
	 */
	public function getLessStrategy(){
		return $this->lessStrategy;
	}
	
	/**
	 * @param LessStrategyInterface $lessStrategy
	 * @param boolean $isDefault
	 */
	public function setLessStrategy(LessStrategyInterface $lessStrategy,$isDefault=false){
		$this->lessStrategy = $lessStrategy;		
		$this->isDefault['lessStrategy'] = $isDefault?true:false;
	}
	
	/**
	 * @return \Bricks\AssetService\ScssStrategy\ScssStrategyInterface
	 */
	public function getScssStrategy(){
		return $this->scssStrategy;
	}
	
	/**
	 * @param ScssStrategyInterface $scssStrategy
	 * @param boolean $isDefault
	 */
	public function setScssStrategy(ScssStrategyInterface $scssStrategy,$isDefault=false){
		$this->scssStrategy = $scssStrategy;		
		$this->isDefault['scssStrategy'] = $isDefault?true:false;
	}
	
	/**
	 * @return boolean
	 */
	public function isScssSupport(){
		return $this->scssSupport;
	}
	
	/**
	 * @param boolean $bool
	 * @param boolean $isDefault
	 */
	public function setScssSupport($bool,$isDefault=false){
		$this->scssSupport = $bool?true:false;
		$this->isDefault['scssSupport'] = $isDefault?true:false;
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
	 * @param boolean $isDefault
	 */
	public function setMinifyCssStrategy(MinifyCssStrategyInterface $minifyCssStrategy,$isDefault=false){
		$this->minifyCssStrategy = $minifyCssStrategy;		
		$this->isDefault['minifyCssStrategy'] = $isDefault?true:false;
	}
	
	/**
	 * @return boolean
	 */
	public function isMinifyCssSupport(){
		return $this->minifyCssSupport;
	}
	
	/**
	 * @param boolean $bool
	 * @param boolean $isDefault
	 */
	public function setIsMinifyCssSupport($bool,$isDefault=false){
		$this->minifyCssSupport = $bool?true:false;
		$this->isDefault['minifyCssSupport'] = $isDefault?true:false;
	}	
	
	/**
	 * @return \Bricks\AssetService\MinifyJsStrategy\MinifyJsStrategyInterface
	 */
	public function getMinifyJsStrategy(){
		return $this->minifyJsStrategy;
	}
	
	/**
	 * @param MinifyJsStrategyInterface $minifyJsStrategy
	 * @param boolean $isDefault
	 */
	public function setMinifyJsStrategy(MinifyJsStrategyInterface $minifyJsStrategy,$isDefault=false){
		$this->minifyJsStrategy = $minifyJsStrategy;		
		$this->isDefault['minifyJsStrategy'] = $isDefault?true:false;
	}
	
	/**
	 * @return boolean
	 */
	public function isMinifyJsSupport(){
		return $this->minifyJsSupport;
	}
	
	/**
	 * @param boolean $bool
	 * @param boolean $isDefault
	 */
	public function setMinifyJsSupport($bool,$isDefault=false){
		$this->minifyJsSupport = $bool?true:false;
		$this->isDefault = $isDefault?true:false;
	}
	
	/**
	 * This method should be called if the asset service change any default value
	 * @param AssetService $as
	 */
	public function defaultsChanged(AssetService $as){
		foreach($this->isDefault AS $key => $value){
			if(true==$value){
				$this->{'set'.ucfirst($key)}($as->{'get'.ucfirst($key)}(),true);
			}
		}
	}
	
}