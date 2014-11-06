<?php

namespace BricksAssetTest\AssetService;

use BricksAssetTest\Mocking\AssetService\PublishAdapter\MockAdapter;
use PHPUnit_Framework_TestCase;
use BricksAssetTest\Bootstrap;
use BricksAssetTest\Mock\ClassLoader;

class AssetServiceTest extends PHPUnit_Framework_TestCase {
	
	public function testGetInstance(){		
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$this->assertInstanceOf('\Bricks\AssetService\AssetService',$as);
	}
	
	public function testGetModuleName(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertEquals('BricksAsset',$module->getModuleName());		
	}
	
	public function testChangeModuleName(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$module->setModuleName('Test');
		$this->assertEquals('Test',$module->getModuleName());
	}
	
	public function testAssetAdapter(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceOf('\Bricks\AssetService\AssetAdapter\FilesystemAdapter',$module->getAssetAdapter());				
	}
	
	public function testGlobalyChangeAssetAdapter(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['assetAdapter'] = 'BricksAssetTest\Mock\Adapter';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceOf('\BricksAssetTest\Mock\Adapter',$module->getAssetAdapter());
		$as->setConfig($before);
	}
	
	public function testSpecificChangeAssetAdapter(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['assetAdapter'] = 'BricksAssetTest\Mock\Adapter';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceOf('\BricksAssetTest\Mock\Adapter',$module->getAssetAdapter());
		$as->setConfig($before);
	}
	
	public function testBothChangeAssetAdapter(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['assetAdapter'] = 'Bricks\AssetService\AssetAdapter\FilesystemAdapter'; 
		$cfg['module_specific']['BricksAsset']['assetAdapter'] = 'BricksAssetTest\Mock\Adapter';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceOf('\BricksAssetTest\Mock\Adapter',$module->getAssetAdapter());
		$as->setConfig($before);
	}
	
	public function testClassLoader(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertEquals('Bricks\AssetService\ClassLoader\ClassLoader',$module->getClassLoaderClass());
		$this->assertInstanceof('\Bricks\AssetService\ClassLoader\ClassLoader',$module->getClassLoader());
	}
	
	public function testGlobalyChangeClassLoader(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		ClassLoader::getInstance(Bootstrap::getServiceManager());		
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['classLoader'] = 'BricksAssetTest\Mock\ClassLoader';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals('BricksAssetTest\Mock\ClassLoader',$module->getClassLoaderClass());
		$this->assertInstanceof('\BricksAssetTest\Mock\ClassLoader',$module->getClassLoader());
		$as->setConfig($before);
	}
	
	public function testSpecificChangeClassLoader(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		ClassLoader::getInstance(Bootstrap::getServiceManager());
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['classLoader'] = 'BricksAssetTest\Mock\ClassLoader';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals('BricksAssetTest\Mock\ClassLoader',$module->getClassLoaderClass());
		$this->assertInstanceof('\BricksAssetTest\Mock\ClassLoader',$module->getClassLoader());
		$as->setConfig($before);
	}
	
	public function testBothChangeClassLoader(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		ClassLoader::getInstance(Bootstrap::getServiceManager());
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['classLoader'] = 'Bricks\AssetService\ClassLoader\ClassLoader';
		$cfg['module_specific']['BricksAsset']['classLoader'] = 'BricksAssetTest\Mock\ClassLoader';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals('BricksAssetTest\Mock\ClassLoader',$module->getClassLoaderClass());
		$this->assertInstanceof('\BricksAssetTest\Mock\ClassLoader',$module->getClassLoader());
		$as->setConfig($before);
	}
	
	public function testWwwrootPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertEquals(realpath('./httpdocs'),$module->getWwwrootPath());
	}
	
	public function testGlobalyChangeWwwrootPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['wwwroot_path'] = realpath('./public2');		
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals(realpath('./public2'),$module->getWwwrootPath());
		$as->setConfig($before);
	}
	
	public function testSpecificChangeWwwrootPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['wwwroot_path'] = realpath('./public2');
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals(realpath('./public2'),$module->getWwwrootPath());
		$as->setConfig($before);
	}
	
	public function testBothChangeWwwrootPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$cfg = $as->getConfig();
		$before = $as->getConfig();
		$cfg['wwwroot_path'] = realpath('./public');
		$cfg['module_specific']['BricksAsset']['wwwroot_path'] = realpath('./public2');
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals(realpath('./public2'),$module->getWwwrootPath());
		$as->setConfig($before);
	}
	
	public function testHttpAssetsPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertEquals('module',$module->getHttpAssetsPath());
	}
	
	public function testGlobalyChangeHttpAssetsPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['http_assets_path'] = 'module2';		
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals('module2',$module->getHttpAssetsPath());
		$as->setConfig($before);
	}
	
	public function testSpecificChangeHttpAssetsPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['http_assets_path'] = 'module2';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals('module2',$module->getHttpAssetsPath());
		$as->setConfig($before);
	}
	
	public function testBothChangeHttpAssetsPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['http_assets_path'] = 'module2';
		$cfg['module_specific']['BricksAsset']['wwwroot_path'] = 'module2';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals('module2',$module->getHttpAssetsPath());
		$as->setConfig($before);
	}
	
	public function testModuleAssetPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');		
		$this->assertEquals(realpath('./public'),$module->getModuleAssetPath());
	}
	
	public function testChangeModuleAssetPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['module_asset_path'] = realpath('./public2');
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals(realpath('./public2'),$module->getModuleAssetPath());
		$as->setConfig($before);
	}
	
	public function testIsAutoPublish(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertTrue($module->isAutoPublish());
	}
	
	public function testChangeGlobalyIsAutoPublish(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['autoPublish'] = false;
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertFalse($module->isAutoPublish());
		$as->setConfig($before);
	}
	
	public function testChangeSpecificIsAutoPublish(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['autoPublish'] = false;
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertFalse($module->isAutoPublish());
		$as->setConfig($before);
	}

	public function testBothSpecificIsAutoPublish(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['autoPublish'] = true;
		$cfg['module_specific']['BricksAsset']['autoPublish'] = false;
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertFalse($module->isAutoPublish());
		$as->setConfig($before);
	}
	
	public function testIsAutoOptimize(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertTrue($module->isAutoOptimize());
	}
	
	public function testChangeGlobalyIsAutoOptimize(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['autoOptimize'] = false;
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertFalse($module->isAutoOptimize());
		$as->setConfig($before);
	}
	
	public function testChangeSpecificIsAutoOptimize(){		
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['autoOptimize'] = false;
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertFalse($module->isAutoOptimize());
		$as->setConfig($before);
	}
	
	public function testBothSpecificIsAutoOptimize(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['autoOptimize'] = true;
		$cfg['module_specific']['BricksAsset']['autoOptimize'] = false;
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertFalse($module->isAutoOptimize());
		$as->setConfig($before);
	}
	
	public function testRemoveStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\Bricks\AssetService\RemoveStrategy\RemoveStrategy',$module->getRemoveStrategy());
	}

	public function testGlobaleChangeRemoveStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['removeStrategy'] = 'BricksAssetTest\Mock\RemoveStrategy';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\BricksAssetTest\Mock\RemoveStrategy',$module->getRemoveStrategy());
		$as->setConfig($before);
	}
	
	public function testSpecificChangeRemoveStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['removeStrategy'] = 'BricksAssetTest\Mock\RemoveStrategy';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\BricksAssetTest\Mock\RemoveStrategy',$module->getRemoveStrategy());
		$as->setConfig($before);
	}	
	
	public function testPublishStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\Bricks\AssetService\PublishStrategy\CopyStrategy',$module->getPublishStrategy());
	}
	
	public function testGlobalyChangePublishStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['publishStrategy'] = 'BricksAssetTest\Mock\PublishStrategy';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\BricksAssetTest\Mock\PublishStrategy',$module->getPublishStrategy());
		$as->setConfig($before);
	}
	
	public function testSpecificChangePublishStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['publishStrategy'] = 'BricksAssetTest\Mock\PublishStrategy';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\BricksAssetTest\Mock\PublishStrategy',$module->getPublishStrategy());
		$as->setConfig($before);
	}
	
	public function testLessStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\Bricks\AssetService\LessStrategy\NeilimeLessphpStrategy',$module->getLessStrategy());
	}
	
	public function testGlobaleChangeLessStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['lessStrategy'] = 'BricksAssetTest\Mock\LessStrategy';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\BricksAssetTest\Mock\LessStrategy',$module->getLessStrategy());
		$as->setConfig($before);
	}
	
	public function testSpecificChangeLessStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['lessStrategy'] = 'BricksAssetTest\Mock\LessStrategy';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\BricksAssetTest\Mock\LessStrategy',$module->getLessStrategy());
		$as->setConfig($before);
	}
	
	public function testScssStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\Bricks\AssetService\ScssStrategy\LeafoScssphpStrategy',$module->getScssStrategy());
	}
	
	public function testGlobaleChangeScssStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['scssStrategy'] = 'BricksAssetTest\Mock\ScssStrategy';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\BricksAssetTest\Mock\ScssStrategy',$module->getScssStrategy());
		$as->setConfig($before);
	}
	
	public function testSpecificChangeScssStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['scssStrategy'] = 'BricksAssetTest\Mock\ScssStrategy';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\BricksAssetTest\Mock\ScssStrategy',$module->getScssStrategy());
		$as->setConfig($before);
	}
	
	public function testMinifyCssStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\Bricks\AssetService\MinifyCssStrategy\MrclayMinifyStrategy',$module->getMinifyCssStrategy());
	}
	
	public function testGlobaleChangeMinifyCssStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['minifyCssStrategy'] = 'BricksAssetTest\Mock\MinifyCssStrategy';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\BricksAssetTest\Mock\MinifyCssStrategy',$module->getMinifyCssStrategy());
		$as->setConfig($before);
	}
	
	public function testSpecificChangeMinifyCssStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['minifyCssStrategy'] = 'BricksAssetTest\Mock\MinifyCssStrategy';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\BricksAssetTest\Mock\MinifyCssStrategy',$module->getMinifyCssStrategy());
		$as->setConfig($before);
	}
	
	public function testMinifyJsStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\Bricks\AssetService\MinifyJsStrategy\MrclayMinifyStrategy',$module->getMinifyJsStrategy());
	}
	
	public function testGlobaleChangeMinifyJsStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['minifyJsStrategy'] = 'BricksAssetTest\Mock\MinifyJsStrategy';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\BricksAssetTest\Mock\MinifyJsStrategy',$module->getMinifyJsStrategy());
		$as->setConfig($before);
	}
	
	public function testSpecificChangeMinifyJsStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$before = $as->getConfig();
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['minifyJsStrategy'] = 'BricksAssetTest\Mock\MinifyJsStrategy';
		$as->setConfig($cfg);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceof('\BricksAssetTest\Mock\MinifyJsStrategy',$module->getMinifyJsStrategy());
		$as->setConfig($before);
	}
	
	/*
	public function testSetStrategy(){
		$as = Di::getInstance()->newInstance('Bricks\AssetService\AssetService');
		$cfg = $as->getConfig();
		$cfg['module_specific']['BricksAsset']['publishStrategy'] = 'BricksAssetTest\Mock\PublishStrategy\NullStrategy';
		$as->setConfig($cfg);
		$cfg = $as->getConfig();
		$this->assertTrue(isset($cfg['module_specific']['BricksAsset']['publishStrategy']));
		$this->assertTrue('BricksAssetTest\Mock\PublishStrategy\NullStrategy'==$cfg['module_specific']['BricksAsset']['publishStrategy']);
		$this->assertInstanceof('BricksAssetTest\Mock\PublishStrategy\NullStrategy',$as->getPublishStrategy('BricksAsset'));
	}
	
	public function testRemovePublishAndOptimize(){
		$as = Di::getInstance()->newInstance('Bricks\AssetService\AssetService');
		$wwwroot_path = $as->getWwwRootPath('BricksAsset');
		$http_assets_path = $as->getHttpAssetsPath('BricksAsset');
		$httpdir = realpath($wwwroot_path).'/'.$http_assets_path.'/BricksAsset';
		if(file_exists($httpdir)){
			$as->remove('BricksAsset');
		}
		$this->assertTrue(!file_exists($httpdir));
		$as->publish('BricksAsset');
		$as->optimize('BricksAsset');
		
		$this->assertFileExists($httpdir.'/css');
		$this->assertFileExists($httpdir.'/css/test.css');		
		$this->assertFileExists($httpdir.'/css/test.less');
		$this->assertFileExists($httpdir.'/css/test.less.css');
		$this->assertFileExists($httpdir.'/css/test.less.min.css');
		$this->assertFileExists($httpdir.'/css/test.scss');
		$this->assertFileExists($httpdir.'/css/test.scss.css');
		$this->assertFileExists($httpdir.'/css/test.scss.min.css');
		$this->assertFileExists($httpdir.'/css/test.sass');
		$this->assertFileExists($httpdir.'/css/test.sass.css');
		$this->assertFileExists($httpdir.'/css/test.sass.min.css');
		$this->assertFileExists($httpdir.'/js/test.js');
		$this->assertFileExists($httpdir.'/js/test.min.js');
		
		$as->remove('BricksAsset');
	}
	*/
	
}