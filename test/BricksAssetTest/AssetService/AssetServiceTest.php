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
	
	public function testConfigInterface(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$this->assertInstanceOf('\Bricks\AssetService\AssetService',$as);
		$config = $as->getConfig();
		
		// store initial state
		$reset = $config;
		
		$aList = array(
			'ClassLoader',
			'AssetAdapter',
			'PublishStrategy',
			'RemoveStrategy',
			'LessStrategy',
			'ScssStrategy',
			'MinifyCssStrategy',
			'MinifyJsStrategy',
		);
		$altCfg = $config;
		foreach($aList as $key){
			$altCfg['module_specific']['BricksAsset'][$key] =
				str_replace('Bricks\AssetService\\'.ucfirst($key),'BricksAssetTest\Mock');
		}		
		
		// test using asset service
		$as->setConfig($altCfg);
		$this->assertEquals($altCfg,$as->getConfig());
		$module = $as->getModule('BricksAsset');
		$this->assertEquals($altCfg['module_specific']['BricksAsset'],$module->getConfig());
		
		// reset
		$as->setConfig($reset);
		$config = $as->getConfig();
		
		// test using module
		foreach($aList AS $key){
			$config['module_specific']['BricksAsset'][$key] =
				str_replace('Bricks\AssetService\\'.ucfirst($key),'BricksAssetTest\Mock');
		}
		$module = $as->getModule('BricksAsset');
		$module->setConfig($config['module_specific']['BricksAsset']);
		
		
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
		$classLoader = $module->getClassLoader();
		$assetAdapter = $classLoader->get('BricksAssetTest\Mock\Adapter');
		$module->setAssetAdapter($assetAdapter);
		$_cfg = $module->getConfig();
		$this->assertEquals('BricksAssetTest\Mock\Adapter',$_cfg['assetAdapter']);
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
		$classLoader = $module->getClassLoader();
		$this->assertInstanceof('\BricksAssetTest\Mock\ClassLoader',$classLoader);
		$as->setConfig($before);
		$module->setClassLoader($classLoader);
		$_cfg = $module->getConfig();
		$this->assertEquals('BricksAssetTest\Mock\ClassLoader',$_cfg['classLoader']);
		$this->assertInstanceOf('\BricksAssetTest\Mock\ClassLoader',$module->getClassLoader());
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
		$module->setWwwrootPath(realpath('./public2'));
		$_cfg = $module->getConfig();
		$this->assertEquals(realpath('./public2'),$cfg['wwwroot_path']);		
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
		$module->setHttpAssetsPath('module2');
		$_cfg = $module->getConfig();
		$this->assertEquals('module2',$cfg['http_assets_path']);
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
		$module->setModuleAssetPath(realpath('./public2'));
		$_cfg = $module->getConfig();
		$this->assertEquals(realpath('./public2'),$_cfg['module_asset_path']);
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
		$module->setIsAutoPublish(true);
		$_cfg = $module->getConfig();
		$this->assertEquals(true,$_cfg['autoPublish']);
		$this->assertEquals(true,$module->isAutoPublish());
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
		$module->setIsAutoOptimize(true);
		$this->assertEquals(true,$_cfg['autoOptimize']);
		$this->assertEquals(true,$module->isAutoOptimize());
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
		$strategy = new \BricksAssetTest\Mock\RemoveStrategy;
		$module->setRemoveStrategy($strategy);
		$_cfg = $module->getConfig();
		$this->assertEquals('BricksAssetTest\Mock\RemoveStrategy',$_cfg['removeStrategy']);
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
		$strategy = new \BricksAssetTest\Mock\PublishStrategy;
		$module->setPublishStrategy($strategy);
		$_cfg = $module->getConfig();
		$this->assertEquals('BricksAssetTest\Mock\PublishStrategy',$_cfg['publishStrategy']);
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
		$strategy = new \BricksAssetTest\Mock\LessStrategy;
		$module->setLessStrategy($strategy);
		$_cfg = $module->getConfig();
		$this->assertEquals('BricksAssetTest\Mock\LessStrategy',$_cfg['lessStrategy']);
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
		$strategy = new \BricksAssetTest\Mock\ScssStrategy;
		$module->setScssStrategy($strategy);
		$_cfg = $module->getConfig();
		$this->assertEquals('BricksAssetTest\Mock\ScssStrategy',$_cfg['scssStrategy']);
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
		$strategy = new \BricksAssetTest\Mock\MinifyCssStrategy;
		$module->setMinifyCssStrategy($strategy);
		$_cfg = $module->getConfig();
		$this->assertEquals('BricksAssetTest\Mock\MinifyCssStrategy',$_cfg['minifyCssStrategy']);
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
		$strategy = new \BricksAssetTest\Mock\MinifyJsStrategy;
		$module->setMinifyJsStrategy($strategy);
		$_cfg = $module->getConfig();
		$this->assertEquals('BricksAssetTest\Mock\MinifyJsStrategy',$_cfg['minifyJsStrategy']);
		$this->assertInstanceof('\BricksAssetTest\Mock\MinifyJsStrategy',$module->getMinifyJsStrategy());
		$as->setConfig($before);
	}
	
	public function testRun1(){
		$as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$module = $as->getModule('BricksAsset');
		$httpdir = realpath($module->getWwwrootPath()).'/'.$module->getHttpAssetsPath().'/BricksAsset';
		
		$as->remove('BricksAsset');
		$this->assertFalse(file_exists($module->getHttpAssetsPath()));
		
		$as->publish('BricksAsset');
		$as->optimize('BricksAsset');
		
		$this->assertTrue(file_exists($httpdir));
		
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
	
}