);<?php

namespace Bricks\AssetService;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Service which allow us to control each module
 */
class AssetService implements ServiceManagerAwareInterface {
	
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
	 * The Service Manager here is needed to load a diffrent class loader.
	 * @var ServiceManager
	 */
	protected $sm;
	
	/**
	 * @param array $config
	 * @param array $loadedModules
	 */
	public function __construct(array $config,array $loadedModules){
		$this->setLoadedModules($loadedModules);
		$this->setConfig($config);
		$this->updateModules();						
	}
	
	/**
	 * @param ServiceManager $serviceManager
	 */
	public function setServiceManager(ServiceManager $serviceManager){
		$this->sm = $serviceManager;
	}
	
	/**
	 * @return \Zend\ServiceManager\ServiceManager
	 */
	public function getServiceManager(){		
		return $this->sm;
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
	 * re-/configures setted up modules
	 */
	protected function updateModules(){
		$cfg = $this->getConfig();		
		foreach($this->getLoadedModules() AS $moduleName){
			if(!isset($cfg['module_specific'][$moduleName])){
				continue;
			}
			$classLoaderClass = ($cfg['module_specific'][$moduleName]['classLoader'])?:$cfg['classLoader'];
			$classLoader = $classLoaderClass::getInstance();				
			$config = array();
			foreach($cfg AS $key => $value){
				if('module_specific'==$key){
					continue;
				}
				$config[$key] = $value;
			}
			$config = array_merge($config,$cfg['module_specific'][$moduleName]);
			if(!count($this->modules)){
				$module = $classLoader->get($this->getAssetModule(),array($config,$moduleName,$classLoader));					
			} else {
				$module = $this->getModule($moduleName);
				$module->setClassLoader($classLoader);
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
		foreach($modules AS $module){
			$module->remove();
			/*
			if(!$this->getAdapter()->hasHttpAssetsPath($moduleName)){
				continue;
			}
			
			$moduleName = $this->filterModuleName($module);
			$wwwroot_path = $this->getWwwrootPath($moduleName);
			$http_assets_path = $this->getHttpAssetsPath($moduleName);
			$removeStrategy = $this->getRemoveStrategy($moduleName);
			
			$path = $this->getAdapter()->realpath($wwwroot_path).'/'.$http_assets_path.'/'.$moduleName;
			if(!$this->getAdapter()->fileExists($path)){
				continue;
			}
			$this->doRemove($removeStrategy,$target);
			*/
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
		foreach($modules AS $module){
			$module->publish();
			/*			
			$moduleName = $this->filterModuleName($module);			
			$module_assets_path = $this->getModuleAssetsPath($moduleName);			
			if(false==$module_assets_path||!$this->getAdapter()->isDir($module_assets_path)){
				continue;
			}
			$wwwroot_path = $this->getWwwrootPath($moduleName);	
			$http_assets_path = $this->getHttpAssetsPath($moduleName);			
			$publishStrategy = $this->getPublishStrategy($moduleName);
			$lessStrategy = $this->getLessStrategy($moduleName);
			$scssStrategy = $this->getScssStrategy($moduleName);
			$lessSupport = $this->getLessSupport($moduleName);
			$scssSupport = $this->getScssSupport($moduleName);
						
			$_assets_path = $this->getAdapter()->realpath($wwwroot_path).'/'.$http_assets_path;			
			if(!$this->getAdapter()->fileExists($_assets_path)){
				throw new RuntimeException('please create folder ('.$_assets_path.')');
			}									
			$_assets_path .= '/'.$moduleName;			
			
			$this->doPublish($publishStrategy,$module_assets_path,$_assets_path);
			if($lessSupport){				
				$this->doLess($lessStrategy,$_assets_path,$_assets_path);
			}
			if($scssSupport){				
				$this->doScss($scssStrategy,$_assets_path,$_assets_path);
			}
			*/
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
		foreach($modules AS $module){
			$module->optimize();
			/*			
			$moduleName = $this->filterModuleName($module);
			$module_assets_path = $this->getModuleAssetsPath($moduleName);
			if(false==$module_assets_path||!$this->getAdapter()->isDir($module_assets_path)){
				continue;
			}
			$wwwroot_path = $this->getWwwrootPath($moduleName);
			$http_assets_path = $this->getHttpAssetsPath($moduleName);			
			$minifyCssStrategy = $this->getMinifyCssStrategy($moduleName);
			$minifyJsStrategy = $this->getMinifyJsStrategy($moduleName);
			$minifyCssSupport = $this->getMinifyCssSupport($moduleName);
			$minifyJsSupport = $this->getMinifyJsSupport($moduleName);			

			$_assets_path = $this->getAdapter()->realpath($wwwroot_path).'/'.$http_assets_path;
			if(!$this->getAdapter()->fileExists($_assets_path)){
				throw new RuntimeException('please create folder ('.$_assets_path.')');
			}			
			$_assets_path .= '/'.$moduleName;
			
			if($minifyCssSupport){
				$this->doMinifyCss($minifyCssStrategy,$_assets_path,$_assets_path);
			}
			if($minifyJsSupport){
				$this->doMinifyJs($minifyJsStrategy,$_assets_path,$_assets_path);
			}	
			*/											
		}
		
	}
		
	/**
	 * @param string $module
	 * @throws RuntimeException	 
	 * @return string
	 *
	public function getModuleAssetsPath($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['module_assets_path'])){
			$path = $assetsCfg['module_specific'][$moduleName]['module_assets_path'];
		} else {
			$path = false;
		}		
		return $path;
	}
	*/
	
	/**
	 * @param string $module
	 * @throws RuntimeException
	 * @return string
	 *
	public function getWwwrootPath($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['wwwroot_path'])){
			$path = $assetsCfg['module_specific'][$moduleName]['wwwroot_path'];
		} elseif(isset($assetsCfg['wwwroot_path'])){
			$path = $assetsCfg['wwwroot_path'];
		} else {
			throw new RuntimeException('could not get wwwroot path for module: '.$moduleName);
		}
		return $path;
	}
	*/
	
	/**
	 * @param 	string $module
	 * @throws 	RuntimeException	 
	 * @return 	string the relative/absolute path (how it is configured)	
	 * 			to the public webroot module dir
	 *
	public function getHttpAssetsPath($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['http_assets_path'])){
			$path = $assetsCfg['module_specific'][$moduleName]['http_assets_path'];
		} elseif(isset($assetsCfg['http_assets_path'])){
			$path = $assetsCfg['http_assets_path'];
		} else {
			throw new RuntimeException('could not get http assets path for module: '.$moduleName);
		}
		return $path;
	}
	*/
	
	/*
	public function getRemoveAdapter($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['removeAdapter'])){
			$adapter = $assetsCfg['module_specific'][$moduleName]['removeAdapter'];
		} elseif(isset($assetsCfg['removeAdapter'])){
			$adapter = $assetsCfg['removeAdapter'];
		} else {
			$adapter = 'Bricks\AssetService\Adapter\FileAdapter';
		}
		if(!class_exists($adapter,true)){
			throw new RuntimeException('Remove adapter ('.$adapter.') not exists');
		}
		if(!isset($this->adapters[$adapter])){
			$this->adapters[$adapter] = Di::getInstance()->get($adapter);
		}
		return $this->strategies['RemoveStrategy'][$strategy];
	}
	*/
	
	/**
	 * @param string $module
	 * @throws RuntimeException
	 * @return \Bricks\AssetService\RemoveStrategy\RemoveStrategyInterface
	 *
	public function getRemoveStrategy($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['removeStrategy'])){
			$strategy = $assetsCfg['module_specific'][$moduleName]['removeStrategy'];
		} elseif(isset($assetsCfg['removeStrategy'])){
			$strategy = $assetsCfg['removeStrategy'];
		} else {
			$strategy = 'Bricks\AssetService\RemoveStrategy\RemoveStrategy';
		}
		if(!class_exists($strategy,true)){
			throw new RuntimeException('Remove strategy ('.$strategy.') not exists');
		}
		if(!isset($this->strategies['RemoveStrategy'][$strategy])){
			$this->strategies['RemoveStrategy'][$strategy] =
			Di::getInstance()->newInstance($strategy,array(),false);
		}
		return $this->strategies['RemoveStrategy'][$strategy];
	}
	*/
	
	/**
	 * @param string $module
	 * @throws RuntimeException
	 * @return \Bricks\AssetService\PublishStrategy\PublishStrategyInterface
	 *
	public function getPublishStrategy($module){		
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['publishStrategy'])){
			$strategy = $assetsCfg['module_specific'][$moduleName]['publishStrategy'];
		} elseif(isset($assetsCfg['publishStrategy'])){
			$strategy = $assetsCfg['publishStrategy'];
		} else {
			$strategy = 'Bricks\AssetService\PublishStrategy\CopyStrategy';			
		}
		if(!class_exists($strategy,true)){
			throw new RuntimeException('Publish strategy ('.$strategy.') not exists');
		}
		if(!isset($this->strategies['PublishStrategy'][$strategy])){			
			$this->strategies['PublishStrategy'][$strategy] = 
				Di::getInstance()->newInstance($strategy,array(),false);
		}		
		return $this->strategies['PublishStrategy'][$strategy];
	}
	*/
	
	/**
	 * @param string $module
	 * @return \Bricks\AssetService\LessStrategy\LessStrategyInterface
	 *
	public function getLessStrategy($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['lessStrategy'])){
			$strategy = $assetsCfg['module_specific'][$moduleName]['lessStrategy'];
		} elseif(isset($assetsCfg['lessStrategy'])){
			$strategy = $assetsCfg['lessStrategy'];
		} else {
			$strategy = 'Bricks\AssetService\LessStrategy\NeilimeLessphpStrategy';
		}
		if(!class_exists($strategy,true)){
			throw new RuntimeException('Less strategy ('.$strategy.') not exists');
		}
		if(!isset($this->strategies['LessStrategy'][$strategy])){
			$this->strategies['LessStrategy'][$strategy] = 
				Di::getInstance()->newInstance($strategy,array(),false);
		}
		return $this->strategies['LessStrategy'][$strategy];
	}
	*/
	
	/**
	 * @param string $module
	 * @return \Bricks\AssetService\ScssStrategy\ScssStrategyInterface
	 *
	public function getScssStrategy($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['scssStrategy'])){
			$strategy = $assetsCfg['module_specific'][$moduleName]['scssStrategy'];
		} elseif(isset($assetsCfg['scssStrategy'])){
			$strategy = $assetsCfg['scssStrategy'];
		} else {
			$strategy = 'Bricks\AssetService\ScssStrategy\LeafoScssphpStrategy';
		}		
		if(!class_exists($strategy,true)){
			throw new RuntimeException('scss strategy ('.$strategy.') not exists');
		}
		if(!isset($this->strategies['ScssStrategy'][$strategy])){
			$this->strategies['ScssStrategy'][$strategy] = 
				Di::getInstance()->newInstance($strategy,array(),false);
		}
		return $this->strategies['ScssStrategy'][$strategy];
	}
	*/
	
	/**
	 * @param string $module
	 * @return \Bricks\AssetService\MinifyCssStrategy\MinifyStrategyInterface
	 *
	public function getMinifyCssStrategy($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['minifyCssStrategy'])){
			$strategy = $assetsCfg['module_specific'][$moduleName]['minifyCssStrategy'];
		} elseif(isset($assetsCfg['minifyCssStrategy'])){
			$strategy = $assetsCfg['minifyCssStrategy'];
		} else {
			$strategy = 'Bricks\AssetService\MinifyCssStrategy\MrclayMinifyStrategy';
		}
		if(!class_exists($strategy,true)){
			throw new RuntimeException('Minify CSS strategy ('.$strategy.') not exists');
		}
		if(!isset($this->strategies['MinifyCssStrategy'][$strategy])){
			$this->strategies['MinifyCssStrategy'][$strategy] = new $strategy();				
		}
		return $this->strategies['MinifyCssStrategy'][$strategy];
	}
	*/
	
	/**
	 * @param string $module
	 * @return \Bricks\AssetService\MinifyJsStrategy\MinifyStrategyInterface
	 *
	public function getMinifyJsStrategy($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['minifyJsStrategy'])){
			$strategy = $assetsCfg['module_specific'][$moduleName]['minifyJsStrategy'];
		} elseif(isset($assetsCfg['minifyJsStrategy'])){
			$strategy = $assetsCfg['minifyJsStrategy'];
		} else {
			$strategy = 'Bricks\AssetService\MinifyJsStrategy\MrclayMinifyStrategy';
		}
		if(!class_exists($strategy,true)){
			throw new RuntimeException('Minify JS strategy ('.$strategy.') not exists');
		}
		if(!isset($this->strategies['MinifyJsStrategy'][$strategy])){
			$this->strategies['MinifyJsStrategy'][$strategy] =
				Di::getInstance()->newInstance($strategy,array(),false);
		}
		return $this->strategies['MinifyJsStrategy'][$strategy];
	}
	*/

	/**
	 * @param string $module
	 * @return boolean
	 *
	public function getLessSupport($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();		
		if(isset($assetsCfg['module_specific'][$moduleName]['lessSupport'])){
			$supported = $assetsCfg['module_specific'][$moduleName]['lessSupport'];
		} elseif(isset($assetsCfg['lessSupport'])){
			$supported = $assetsCfg['lessSupport'];
		} else {
			$supported = false;
		}
		return $supported;
	}
	*/
	
	/**
	 * @param string $module
	 * @return boolean
	 *
	public function getScssSupport($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['scssSupport'])){
			$supported = $assetsCfg['module_specific'][$moduleName]['scssSupport'];
		} elseif(isset($assetsCfg['scssSupport'])){
			$supported = $assetsCfg['scssSupport'];
		} else {
			$supported = false;
		}
		return $supported;
	}
	*/
	
	/**
	 * @param string $module
	 * @return boolean
	 *
	public function getMinifyCssSupport($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['minifyCssSupport'])){
			$supported = $assetsCfg['module_specific'][$moduleName]['minifyCssSupport'];
		} elseif(isset($assetsCfg['minifyCssSupport'])){
			$supported = $assetsCfg['minifyCssSupport'];
		} else {
			$supported = false;
		}
		return $supported;
	}
	*/
	
	/**
	 * @param string $module
	 * @return boolean
	 *
	public function getMinifyJsSupport($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['minifyJsSupport'])){
			$supported = $assetsCfg['module_specific'][$moduleName]['minifyJsSupport'];
		} elseif(isset($assetsCfg['minifyJsSupport'])){
			$supported = $assetsCfg['minifyJsSupport'];
		} else {
			$supported = false;
		}
		return $supported;
	}
	*/
	
	/**
	 * @param string $module
	 * @return boolean
	 *
	public function getIsAutoPublish($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['autoPublish'])){
			$supported = $assetsCfg['module_specific'][$moduleName]['autoPublish'];
		} else {
			$supported = false;
		}
		return $supported;
	}
	*/
	
	/**
	 * @param string $module
	 * @return boolean
	 *
	public function getIsAutoOptimize($module){
		$moduleName = $this->filterModuleName($module);
		$assetsCfg = $this->getConfig();
		if(isset($assetsCfg['module_specific'][$moduleName]['autoOptimize'])){
			$supported = $assetsCfg['module_specific'][$moduleName]['autoOptimize'];
		} else {
			$supported = false;
		}
		return $supported;
	}
	*/
	
	/**
	 * @param RemoveStrategyInterface $strategy
	 * @param string $target
	 *
	public function doRemove(RemoveStrategyInterface $strategy,$target){
		$strategy->remove($target);
	}
	*/
	
	/**
	 * @param PublishStrategyInterface $strategy
	 * @param string $source
	 * @param string $target
	 *
	public function doPublish(PublishStrategyInterface $strategy,$source,$target){		
		$strategy->publish($source,$target);
	}
	*/
	
	/**
	 * @param LessStrategyInterface $strategy
	 * @param string $source
	 * @param string $target
	 *
	public function doLess(LessStrategyInterface $strategy,$source,$target){
		$strategy->less($source,$target);
	}
	*/
	
	/**
	 * @param ScssStrategyInterface $strategy
	 * @param string $source 
	 * @param string $target 
	 *
	public function doScss(ScssStrategyInterface $strategy,$source,$target){
		$strategy->scss($source,$target);
	}
	*/
	
	/**
	 * @param MinifyCssStrategyInterface $strategy
	 * @param string $source 
	 * @param string $target 
	 *
	public function doMinifyCss(MinifyCssStrategyInterface $strategy,$source,$target){
		$strategy->minify($source,$target);
	}
	*/
	
	/**
	 * @param MinifyJsStrategyInterface $strategy
	 * @param string $source
	 * @param string $target
	 *
	public function doMinifyJs(MinifyJsStrategyInterface $strategy,$source,$target){
		$strategy->minify($source,$target);				
	}
	*/
	
	/**
	 * Make sure the filename is valid
	 * 
	 * @param string $name
	 * @return string|false
	 *
	public function filterModuleName($name){
		if(preg_match('#^[a-zA-Z]{1}[a-zA-Z0-9._-]*$#',$name)){
			return $name;
		}
		return false;		
	}
	*/
	
}