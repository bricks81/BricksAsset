<?php

namespace Bricks\AssetService;


use Bricks\AssetService\MinifyAdapter\MinifyAdapterInterface;
use Bricks\AssetService\LessAdapter\LessAdapterInterface;
use Bricks\AssetService\PublishAdapter\PublishAdapterInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use \ReflectionClass;
use \RuntimeException;
use Bricks\AssetService\ScssAdapter\ScssAdapterInterface;
use Bricks\File\Directory;

class AssetService implements FactoryInterface, ServiceManagerAwareInterface {
	
	/**
	 * @var ServiceManager
	 */
	protected $serviceManager;
	
	protected $config = array();
	
	protected $adapters = array();
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator){
		$config = $serviceLocator->get('configuration');
		if(!isset($config['Bricks']['BricksAsset'])){
			throw new RuntimeException('please configure bricks asset service');			
		}
		$this->setConfig($config['Bricks']['BricksAsset']);		
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\ServiceManagerAwareInterface::setServiceManager()
	 */
	public function setServiceManager(ServiceManager $serviceManager){
		$this->serviceManager = $serviceManager;
	}
	
	/**
	 * @return ServiceManager
	 */
	public function getServiceManager(){
		return $this->serviceManager;
	}
	
	/**
	 * @param array $config
	 */
	public function setConfig($config){
		$this->config = $config;
	}
	
	/**
	 * @return array
	 */
	public function getConfig(){	
		return $this->config;
	}
	
	/**
	 * @param array $modules
	 * @throws \RuntimeException
	 */
	public function publish(array $modules=array()){
		$config = $this->getServiceManager()->get('ApplicationConfig');
		
		$mm = $this->getServiceManager()->get('ModuleManager');
		if(!count($modules)){
			$modules = $mm->getLoadedModules();
		}
		foreach($modules AS $module){
			if(!is_object($module)){	
				$module = $mm->getModule($module);
			}			
			
			$class = get_class($module);
			$ref = new ReflectionClass($class);
			$filename = $ref->getFileName();
			$dir = dirname($filename);
			$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
			
			$module_assets_subpath = $this->getModuleAssetsSubpath($module);
			$wwwroot_path = $this->getWwwrootPath($module);	
			$http_assets_path = $this->getHttpAssetsPath($module);			
			$publishAdapter = $this->getPublishAdapter($module);
			$lessAdapter = $this->getLessAdapter($module);
			$scssAdapter = $this->getScssAdapter($module);
			$lessSupport = $this->getLessSupport($module);
			$scssSupport = $this->getScssSupport($module);
						
			$_module_assets_path = realpath(rtrim($dir,'/').'/'.$module_assets_subpath);
			if(!file_exists($_module_assets_path)){				
				continue;
			}
			
			$_assets_path = realpath($wwwroot_path).'/'.$http_assets_path;			
			if(!file_exists($_assets_path)){
				throw new RuntimeException('please create folder ('.$_assets_path.')');
			}									
			$_assets_path .= '/'.$moduleName;
			
			$this->doPublish($publishAdapter,$_module_assets_path,$_assets_path);
			if($lessSupport){
				$this->doLess($lessAdapter,$_assets_path,$_assets_path);
			}
			if($scssSupport){				
				$this->doScss($scssAdapter,$_assets_path,$_assets_path);
			}
		}
		
	}
	
	/**
	 * @param array $module
	 * @throws RuntimeException
	 */
	public function optimize(array $modules=array()){
		$config = $this->getServiceManager()->get('ApplicationConfig');
		
		$mm = $this->getServiceManager()->get('ModuleManager');
		if(!count($modules)){
			$modules = $mm->getLoadedModules();
		}		
		foreach($modules AS $module){			
			if(!is_object($module)){	
				$module = $mm->getModule($module);
			}			
			
			$class = get_class($module);
			$ref = new ReflectionClass($class);
			$filename = $ref->getFileName();
			$dir = dirname($filename);
			$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
				
			$wwwroot_path = $this->getWwwrootPath($module);
			$http_assets_path = $this->getHttpAssetsPath($module);			
			$minifyAdapter = $this->getMinifyAdapter($module);
			$minifyCssSupport = $this->getMinifyCssSupport($module);
			$minifyJsSupport = $this->getMinifyJsSupport($module);			
				
			if(!file_exists($http_assets_path)){
				throw new RuntimeException('please create folder ('.$http_assets_path.')');
			}
			$_assets_path = realpath($wwwroot_path.'/'.$http_assets_path);
			$_assets_path .= '/'.$moduleName;
			
			if($minifyCssSupport){
				$this->doMinifyCss($minifyAdapter,$_assets_path,$_assets_path);
			}
			if($minifyJsSupport){
				$this->doMinifyJs($minifyAdapter,$_assets_path,$_assets_path);
			}												
		}
		
	}
		
	/**
	 * @param object $module
	 * @throws RuntimeException	 
	 * @return string the subpath below the module directory
	 */
	public function getModuleAssetsSubpath($module){
		$assetsCfg = $this->getConfig();			
		
		$class = get_class($module);
		$ref = new ReflectionClass($class);
		$filename = $ref->getFileName();
		$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
		
		if(isset($assetsCfg['module_specific'][$moduleName]['module_assets_path'])){
			$subpath = $assetsCfg['module_specific'][$moduleName]['module_assets_path'];
		} elseif(isset($assetsCfg['module_assets_path'])){
			$subpath = $assetsCfg['module_assets_path'];
		} else {
			throw new RuntimeException('could not get module assets subpath for module: '.$moduleName);
		}		
		return $subpath;
	}
	
	/**
	 * @param object $module
	 * @throws RuntimeException
	 * @return string
	 */
	public function getWwwrootPath($module){
		$assetsCfg = $this->getConfig();
		
		$class = get_class($module);
		$ref = new ReflectionClass($class);
		$filename = $ref->getFileName();
		$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
		
		if(isset($assetsCfg['module_specific'][$moduleName]['wwwroot_path'])){
			$path = $assetsCfg['module_specific'][$moduleName]['wwwroot_path'];
		} elseif(isset($assetsCfg['wwwroot_path'])){
			$path = $assetsCfg['wwwroot_path'];
		} else {
			throw new RuntimeException('could not get wwwroot path for module: '.$moduleName);
		}
		return $path;
	}
	
	/**
	 * @param 	object $module
	 * @throws 	RuntimeException	 
	 * @return 	string the relative/absolute path (how it is configured)	
	 * 			to the public webroot module dir
	 */
	public function getHttpAssetsPath($module){
		$assetsCfg = $this->getConfig();
		
		$class = get_class($module);
		$ref = new ReflectionClass($class);
		$filename = $ref->getFileName();
		$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
		
		if(isset($assetsCfg['module_specific'][$moduleName]['http_assets_path'])){
			$path = $assetsCfg['module_specific'][$moduleName]['http_assets_path'];
		} elseif(isset($assetsCfg['http_assets_path'])){
			$path = $assetsCfg['http_assets_path'];
		} else {
			throw new RuntimeException('could not get http assets path for module: '.$moduleName);
		}
		return $path;
	}
	
	/**
	 * @param object $module
	 * @throws RuntimeException
	 * @return \Bricks\AssetService\PublishAdapter\PublishAdapterInterface
	 */
	public function getPublishAdapter($module){
		$assetsCfg = $this->getConfig();
		$class = get_class($module);
		$ref = new ReflectionClass($class);
		$filename = $ref->getFileName();
		$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
	
		if(isset($assetsCfg['module_specific'][$moduleName]['publishAdapter'])){
			$adapter = $assetsCfg['module_specific'][$moduleName]['publishAdapter'];
		} elseif(isset($assetsCfg['publishAdapter'])){
			$adapter = $assetsCfg['publishAdapter'];
		} else {
			$adapter = 'Bricks\AssetService\PublishAdapter\CopyAdapter';			
		}
		if(!class_exists($adapter,true)){
			throw new RuntimeException('Publish adapter ('.$adapter.') not exists');
		}
		if(!isset($this->adapters['PublishAdapter'][$adapter])){
			$this->adapters['PublishAdapter'][$adapter] = new $adapter();
		}
		return $this->adapters['PublishAdapter'][$adapter];
	}
	
	/**
	 * @param object $module
	 * @return \Bricks\AssetService\MinifyAdapter\MinifyAdapterInterface
	 */
	public function getLessAdapter($module){
		$assetsCfg = $this->getConfig();
		$class = get_class($module);
		$ref = new ReflectionClass($class);
		$filename = $ref->getFileName();
		$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
	
		if(isset($assetsCfg['module_specific'][$moduleName]['lessAdapter'])){
			$adapter = $assetsCfg['module_specific'][$moduleName]['lessAdapter'];
		} elseif(isset($assetsCfg['lessAdapter'])){
			$adapter = $assetsCfg['lessAdapter'];
		} else {
			$adapter = 'Bricks\AssetService\LessAdapter\NeilimeLessphpAdapter';
		}
		if(!class_exists($adapter,true)){
			throw new RuntimeException('Less adapter ('.$adapter.') not exists');
		}
		if(!isset($this->adapters['LessAdapter'][$adapter])){
			$this->adapters['LessAdapter'][$adapter] = new $adapter();
		}
		return $this->adapters['LessAdapter'][$adapter];
	}
	
	/**
	 * @param object $module
	 * @return \Bricks\AssetService\ScssAdapter\ScssAdapterInterface
	 */
	public function getScssAdapter($module){
		$assetsCfg = $this->getConfig();
		$class = get_class($module);
		$ref = new ReflectionClass($class);
		$filename = $ref->getFileName();
		$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
	
		if(isset($assetsCfg['module_specific'][$moduleName]['scssAdapter'])){
			$adapter = $assetsCfg['module_specific'][$moduleName]['scssAdapter'];
		} elseif(isset($assetsCfg['scssAdapter'])){
			$adapter = $assetsCfg['scssAdapter'];
		} else {
			$adapter = 'Bricks\AssetService\ScssAdapter\LeafoScssphpAdapter';
		}		
		if(!class_exists($adapter,true)){
			throw new RuntimeException('scss adapter ('.$adapter.') not exists');
		}
		if(!isset($this->adapters['ScssAdapter'][$adapter])){
			$this->adapters['ScssAdapter'][$adapter] = new $adapter();
		}
		return $this->adapters['ScssAdapter'][$adapter];
	}
	
	/**
	 * @param object $module
	 * @return \Bricks\AssetService\LessAdapter\LessAdapterInterface
	 */
	public function getMinifyAdapter($module){
		$assetsCfg = $this->getConfig();
		$class = get_class($module);
		$ref = new ReflectionClass($class);
		$filename = $ref->getFileName();
		$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
	
		if(isset($assetsCfg['module_specific'][$moduleName]['minifyAdapter'])){
			$adapter = $assetsCfg['module_specific'][$moduleName]['minifyAdapter'];
		} elseif(isset($assetsCfg['minifyAdapter'])){
			$adapter = $assetsCfg['minifyAdapter'];
		} else {
			$adapter = 'Bricks\AssetService\MinifyAdapter\MrclayMinifyAdapter';
		}
		if(!class_exists($adapter,true)){
			throw new RuntimeException('Minify adapter ('.$adapter.') not exists');
		}
		if(!isset($this->adapters['MinifyAdapter'][$adapter])){
			$this->adapters['MinifyAdapter'][$adapter] = new $adapter();
		}
		return $this->adapters['MinifyAdapter'][$adapter];
	}
	
	/**
	 * @param string $module
	 * @return boolean
	 */
	public function getLessSupport($module){
		$assetsCfg = $this->getConfig();
		$class = get_class($module);
		$ref = new ReflectionClass($class);
		$filename = $ref->getFileName();
		$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
		
		if(isset($assetsCfg['module_specific'][$moduleName]['lessSupport'])){
			$supported = $assetsCfg['module_specific'][$moduleName]['lessSupport'];
		} elseif(isset($assetsCfg['lessSupport'])){
			$supported = $assetsCfg['lessSupport'];
		} else {
			$supported = false;
		}
		return $supported;
	}
	
	/**
	 * @param object $module
	 * @return boolean
	 */
	public function getScssSupport($module){
		$assetsCfg = $this->getConfig();
		$class = get_class($module);
		$ref = new ReflectionClass($class);
		$filename = $ref->getFileName();
		$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
	
		if(isset($assetsCfg['module_specific'][$moduleName]['scssSupport'])){
			$supported = $assetsCfg['module_specific'][$moduleName]['scssSupport'];
		} elseif(isset($assetsCfg['scssSupport'])){
			$supported = $assetsCfg['scssSupport'];
		} else {
			$supported = false;
		}
		return $supported;
	}
	
	/**
	 * @param object $module
	 * @return boolean
	 */
	public function getMinifyCssSupport($module){
		$assetsCfg = $this->getConfig();
		$class = get_class($module);
		$ref = new ReflectionClass($class);
		$filename = $ref->getFileName();
		$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
	
		if(isset($assetsCfg['module_specific'][$moduleName]['minifyCssSupport'])){
			$supported = $assetsCfg['module_specific'][$moduleName]['minifyCssSupport'];
		} elseif(isset($assetsCfg['minifyCssSupport'])){
			$supported = $assetsCfg['minifyCssSupport'];
		} else {
			$supported = false;
		}
		return $supported;
	}
	
	/**
	 * @param object $module
	 * @return boolean
	 */
	public function getMinifyJsSupport($module){
		$assetsCfg = $this->getConfig();
		$class = get_class($module);
		$ref = new ReflectionClass($class);
		$filename = $ref->getFileName();
		$moduleName = $this->filterModuleName(basename(dirname(str_replace('\\','/',$class))));
	
		if(isset($assetsCfg['module_specific'][$moduleName]['minifyJsSupport'])){
			$supported = $assetsCfg['module_specific'][$moduleName]['minifyJsSupport'];
		} elseif(isset($assetsCfg['minifyJsSupport'])){
			$supported = $assetsCfg['minifyJsSupport'];
		} else {
			$supported = false;
		}
		return $supported;
	}
	
	/**
	 * @param PublishAdapterInterface $adapter
	 * @param string $source the source path of the directory
	 * @param string $target the target directory where it will be copied
	 */
	public function doPublish(PublishAdapterInterface $adapter,$source,$target){
		$adapter->publish($source,$target);
	}
	
	/**
	 * @param LessAdapterInterface $adapter
	 * @param string $source a directory
	 * @param string $target a directory
	 */
	public function doLess(LessAdapterInterface $adapter,$source,$target){
		$adapter->less($source,$target);
	}
	
	/**
	 * @param ScssAdapterInterface $adapter
	 * @param string $source a directory
	 * @param string $target a directory
	 */
	public function doScss(ScssAdapterInterface $adapter,$source,$target){
		$adapter->scss($source,$target);
	}
	
	/**
	 * @param MinifyAdapterInterface $adapter
	 * @param string $source a directory
	 * @param string $target a directory
	 */
	public function doMinifyCss(MinifyAdapterInterface $adapter,$source,$target){
		$adapter->minifyCss($source,$target);
	}
	
	/**
	 * @param MinifyAdapterInterface $adapter
	 * @param string $source a directory
	 * @param string $target a directory
	 */
	public function doMinifyJs(MinifyAdapterInterface $adapter,$source,$target){
		$adapter->minifyJs($source,$target);
	}
	
	/**
	 * Make sure the filename is valid
	 * 
	 * @param string $name
	 * @return string|false
	 */
	public function filterModuleName($name){
		if(preg_match('#^[a-zA-Z0-9._-]*$#',$name)){
			return $name;
		}
		return false;		
	}
	
}