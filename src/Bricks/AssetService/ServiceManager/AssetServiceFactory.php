<?php

namespace Bricks\AssetService\ServiceManager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Bricks\AssetService\AssetConfig;
use Bricks\AssetService\AssetService;
use Bricks\AssetService\ClassLoader\ClassLoader;

class AssetServiceFactory implements FactoryInterface {
	
	public function createService(ServiceLocatorInterface $serviceLocator){
		$cfg = $serviceLocator->get('Config')['Bricks']['BricksAsset'];
		
		// initilize the class loaders
		$loaderClass = $cfg['classLoader'];
		$loaderClass::getInstance($serviceLocator);
		// configured modules
		$loadedModules = array();
		foreach($cfg['moduleSpecific'] AS $moduleName => $mConfig){
			array_push($loadedModules,$moduleName);
			if(isset($mConfig['classLoader'])){
				$loaderClass = $mConfig['classLoader'];
				$loaderClass::getInstance($serviceLocator);				
			}
		}
		
		// return the service
		$as = new AssetService($cfg,$loadedModules);
		return $as;
	}
	
}