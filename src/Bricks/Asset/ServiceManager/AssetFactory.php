<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *  
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Asset\ServiceManager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssetFactory implements FactoryInterface {
	
	public function createService(ServiceLocatorInterface $sl){
		$config = $sl->get('BricksConfig')->getConfig('BricksAsset');
		$classLoader = $sl->get('BricksClassLoader')->getClassLoader('BricksAsset');
		$loadedModules = array_keys($config->getConfig()->getArray()['BricksConfig']['BricksAsset']);
		$service = $classLoader->getSingleton(__CLASS__,__FUNCTION__,'assetClass','BricksAsset',array(
			'BricksConfig' => $config,
			'BricksClassLoader' => $classLoader,
			'loadedModules' => $loadedModules,
			'ServiceLocator' => $sl			
		));
		return $service;
	}
	
}