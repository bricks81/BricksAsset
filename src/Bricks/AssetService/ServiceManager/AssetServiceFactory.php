<?php

namespace Bricks\AssetService\ServiceManager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Bricks\AssetService\AssetConfig;
use Bricks\AssetService\AssetService;
use Bricks\AssetService\ClassLoader\ClassLoader;

class AssetServiceFactory implements FactoryInterface {
	
	public function createService(ServiceLocatorInterface $serviceLocator){
		$loadedModules = $serviceLocator->get('ApplicationConfig')['modules'];		
		$cfg = $serviceLocator->get('Config')['Bricks']['BricksAsset'];
		
		// initilize the class loaders
		$loaderClass = $cfg['classLoader'];
		$loaderClass::getInstance($serviceLocator);
		foreach($cfg AS $key => $value){
			if(!is_array($value)){
				continue;
			}
			if(isset($value['classLoader'])){
				$loaderClass = $value['classLoader'];
				$loaderClass::getInstance($serviceLocator);
			}
		}
		
		// return the service
		$as = new AssetService($cfg,$loadedModules);
		return $as;
	}
	
}