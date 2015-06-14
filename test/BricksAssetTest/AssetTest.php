<?php

namespace BricksAssetTest;

use PHPUnit_Framework_TestCase;
use BricksAssetTest\Bootstrap;

class AssetTest extends PHPUnit_Framework_TestCase {
	
	public function testGetInstance(){
		$config = Bootstrap::getServiceManager()->get('BricksConfig')->getConfig('BricksAsset');
		$classLoader = Bootstrap::getServiceManager()->get('BricksClassLoader')->getClassLoader('BricksAsset');
		$loadedModules = Bootstrap::getServiceManager()->get('ModuleManager')->getModules();
		$service = $classLoader->newInstance(__CLASS__,__FUNCTION__,'assetClass','BricksAsset',array(
			'BricksConfig' => $config,
			'BricksClassLoader' => $classLoader,
			'loadedModules' => $loadedModules
		));
		$this->assertInstanceOf('Bricks\Asset\Asset',$service);
	}
	
}