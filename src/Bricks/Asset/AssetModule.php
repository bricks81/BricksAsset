<?php

namespace Bricks\Asset;

use Bricks\Asset\ClassLoader\ClassLoaderInterface;
use Bricks\Asset\AssetAdapter\AssetAdapterInterface;
use Bricks\Asset\RemoveStrategy\RemoveStrategyInterface;
use Bricks\Asset\PublishStrategy\PublishStrategyInterface;
use Bricks\Asset\LessStrategy\LessStrategyInterface;
use Bricks\Asset\ScssStrategy\ScssStrategyInterface;
use Bricks\Asset\MinifyCssStrategy\MinifyCssStrategyInterface;
use Bricks\Asset\MinifyJsStrategy\MinifyJsStrategyInterface;

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
	
	/**
	 * An array containing regular expressions to excluded files
	 * @var array
	 */
	protected $exclude = array();
	
	protected $isDefault = array();
	
	/**
	 * @var \Bricks\Asset\ClassLoader\ClassLoaderInterface
	 */
	protected $classLoader;
	
	/**
	 * @var \Bricks\Asset\AssetAdapter\AssetAdapterInterface
	 */
	protected $assetAdapter;
	
	/**
	 * @var \Bricks\Asset\PublishStrategy\PublishStrategyInterface
	 */
	protected $publishStrategy;
	
	/**
	 * @var \Bricks\Asset\RemoveStrategy\RemoveStrategyInterface
	 */
	protected $removeStrategy;
	
	/**
	 * @var \Bricks\Asset\LessStrategy\LessStrategyInterface
	 */
	protected $lessStrategy;
	
	/**
	 * @var \Bricks\Asset\ScssStrategy\ScssStrategyInterface
	 */
	protected $scssStrategy;
	
	/**
	 * @var \Bricks\Asset\MinifyCssStrategy\MinifyCssStrategyInterface
	 */
	protected $minifyCssStrategy;
	
	/**
	 * @var \Bricks\Asset\MinifyJsStrategy\MinifyJsStrategyInterface
	 */
	protected $minifyJsStrategy;
	
	/**
	 * @param array $asDefaults Asset Service default config as array
	 * @param array $config The modules specific configuration
	 * @param string $moduleName
	 */
	public function __construct(array $asDefaults,array $config,$moduleName){
		$this->moduleName = $moduleName;
		$defaults = array(
			'autoPublish' => false,
			'autoOptimize' => false,
			'lessSupport' => false,
			'scssSupport' => false,
			'minifyCssSupport' => false,
			'minifyJsSupport' => false,
			'wwwRootPath' => null,
			'httpAssetsPath' => null,
			'moduleAssetsPath' => null,
			'classLoader' => 'Bricks\Asset\ClassLoader\ClassLoader',
			'assetAdapter' => 'Bricks\Asset\AssetAdapter\FilesystemAdapter',
			'publishStrategy' => 'Bricks\Asset\PublishStrategy\CopyStrategy',
			'removeStrategy' => 'Bricks\Asset\RemoveStrategy\RemoveStrategy',
			'lessStrategy' => 'Bricks\Asset\LessStrategy\NeilimeLessphpStrategy',
			'scssStrategy' => 'Bricks\Asset\ScssStrategy\LeafoScssphpStrategy',
			'minifyCssStrategy' => 'Bricks\Asset\MinifyCssStrategy\MrclayMinifyStrategy',
			'minifyJsStrategy' => 'Bricks\Asset\MinifyJsStrategy\MrclayMinifyStrategy',
			'exclude' => array(),
		);		
		
		$prepared = array();
		
		foreach($defaults AS $key => $default){
			if(isset($config[$key])){
				$prepared[$key] = $config[$key];
				$this->isDefault[$key] = false;
			} else {
				$prepared[$key] = $asDefaults[$key];
				$this->isDefault[$key] = true;
			}
		}
		
		$solved = array();
		
		// fetch the class loader
		foreach($prepared AS $key => $value){
			if('classLoader'==$key){
				$solved[$key] = $value::getInstance();								
			}
		}
		
		// create the parts and set them
		foreach($defaults AS $key => $default){
			if('classLoader'==$key){
				continue;
			}
			if(is_bool($defaults[$key]) || is_null($defaults[$key]) || 'exclude' == $key){
				$var = $prepared[$key];				
			} else {
				$var = $solved['classLoader']->getServiceLocator()->get('Di')->get($prepared[$key]);				
			}			
			$this->{'set'.ucfirst($key)}($var,$this->isDefault[$key]);			
		}		
	}
	
	/**
	 * @return string
	 */
	public function getModuleName(){
		return $this->moduleName;
	}
	
	/**
	 * @return \Bricks\Asset\ClassLoader\ClassLoaderInterface
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
	public function getAutoPublish(){
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
		if($this->getAutoPublish()){
			$this->publish();
		}		
	}
	
	/**
	 * @return boolean
	 */
	public function getAutoOptimize(){
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
		if($this->getAutoOptimize()){
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
	 * @return \Bricks\Asset\AssetServiceAwareInterface
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
	 */
	public function setModuleAssetsPath($absolutePath){
		$this->moduleAssetsPath = $absolutePath;				
	}
	
	/**
	 * @param array $exclude containing an array of regular expressions
	 * @param bool $isDefault
	 */
	public function setExclude(array $exclude,$isDefault=false){
		$this->exclude = $exclude;
		$this->isDefault['exclude'] = $isDefault?true:false;
	}
	
	/**
	 * @return array
	 */
	public function getExclude(){
		return $this->exclude;
	}
	
	public function remove(){
		$strategy = $this->getRemoveStrategy();
		$strategy->remove($this);
	}
	
	/**
	 * @return \Bricks\Asset\RemoveStrategy\RemoveStrategyInterface
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
	 * @return \Bricks\Asset\PublishStrategy\PublishStrategyInterface
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
		if($this->getLessSupport()){
			$strategy = $this->getLessStrategy();
			$strategy->less($this);
		}
		if($this->getScssSupport()){
			$strategy = $this->getScssStrategy();
			$strategy->scss($this);
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
	 * @param boolean $isDefault 
	 */
	public function setLessSupport($bool,$isDefault=false){
		$this->lessSupport = $bool?true:false;
		$this->isDefault['lessSupport'] = $isDefault?true:false;
	}
	
	/**
	 * @return \Bricks\Asset\LessStrategy\LessStrategyInterface
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
	 * @return \Bricks\Asset\ScssStrategy\ScssStrategyInterface
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
	public function getScssSupport(){
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
		if($this->getMinifyCssSupport()){
			$strategy = $this->getMinifyCssStrategy();
			$strategy->minify($this);
		}
		if($this->getMinifyJsSupport()){
			$strategy = $this->getMinifyJsStrategy();
			$strategy->minify($this);
		}
	}
	
	/**
	 * @return \Bricks\Asset\MinifyCssStrategy\MinifyCssStrategyInterface
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
	public function getMinifyCssSupport(){
		return $this->minifyCssSupport;
	}
	
	/**
	 * @param boolean $bool
	 * @param boolean $isDefault
	 */
	public function setMinifyCssSupport($bool,$isDefault=false){
		$this->minifyCssSupport = $bool?true:false;
		$this->isDefault['minifyCssSupport'] = $isDefault?true:false;
	}	
	
	/**
	 * @return \Bricks\Asset\MinifyJsStrategy\MinifyJsStrategyInterface
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
	public function getMinifyJsSupport(){		
		return $this->minifyJsSupport;
	}
	
	/**
	 * @param boolean $bool
	 * @param boolean $isDefault
	 */
	public function setMinifyJsSupport($bool,$isDefault=false){
		$this->minifyJsSupport = $bool?true:false;
		$this->isDefault['minifyJsSupport'] = $isDefault?true:false;
	}
	
	/**
	 * This method should be called if the asset service change any default value
	 * @param AssetService $as
	 */
	public function defaultsChanged(AssetService $as){
		foreach($this->isDefault AS $key => $value){
			if(false==$value){
				continue;
			}
			$var = $as->{'get'.ucfirst($key)}();
			if(null===$var){
				continue;
			}			
			$this->{'set'.ucfirst($key)}($var,true);			
		}
	}
	
}