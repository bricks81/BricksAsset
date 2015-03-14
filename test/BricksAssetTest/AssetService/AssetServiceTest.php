<?php

namespace BricksAssetTest\AssetService;

use BricksAssetTest\Mocking\AssetService\PublishAdapter\MockAdapter;
use PHPUnit_Framework_TestCase;
use BricksAssetTest\Bootstrap;
use BricksAssetTest\Mock\ClassLoader;
use BricksAssetTest;

class AssetServiceTest extends PHPUnit_Framework_TestCase {
	
	public function testGetInstance(){		
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
	}
	
	public function testGetModuleName(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$module = $as->getModule('BricksAsset');
		$this->assertEquals('BricksAsset',$module->getModuleName());
	}
	
	public function testConfigClassLoader(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);		
		$classLoader = BricksAssetTest\Mock\ClassLoader::getInstance(Bootstrap::getServiceManager());
		$oldClassLoader = $as->getClassLoader();		
		$as->setClassLoader($classLoader);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceOf('\BricksAssetTest\Mock\ClassLoader',$as->getClassLoader());
		$this->assertInstanceOf('\BricksAssetTest\Mock\ClassLoader',$module->getClassLoader());
		$module->setClassLoader($classLoader);
		$as->setClassLoader($oldClassLoader);
		$this->assertInstanceOf('\Bricks\Asset\ClassLoader\ClassLoader',$as->getClassLoader());
		$this->assertInstanceOf('\BricksAssetTest\Mock\ClassLoader',$module->getClassLoader());
		$module->setClassLoader($oldClassLoader,true);
		$as->setClassLoader($oldClassLoader);
	}
	
	public function testConfigAssetAdapter(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$assetAdapter = new BricksAssetTest\Mock\AssetAdapter();
		$oldAssetAdapter = $as->getAssetAdapter();
		$as->setAssetAdapter($assetAdapter);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceOf('\BricksAssetTest\Mock\AssetAdapter',$as->getAssetAdapter());
		$this->assertInstanceOf('\BricksAssetTest\Mock\AssetAdapter',$module->getAssetAdapter());
		$module->setAssetAdapter($assetAdapter);
		$as->setAssetAdapter($oldAssetAdapter);
		$this->assertInstanceOf('\Bricks\Asset\AssetAdapter\FilesystemAdapter',$as->getAssetAdapter());
		$this->assertInstanceOf('\BricksAssetTest\Mock\AssetAdapter',$module->getAssetAdapter());
		$module->setAssetAdapter($oldAssetAdapter,true);
		$as->setAssetAdapter($oldAssetAdapter);
	}
		
	public function testConfigPublishStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');				
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$publishStrategy = new BricksAssetTest\Mock\PublishStrategy();
		$oldPublishStrategy = $as->getPublishStrategy();		
		$as->setPublishStrategy($publishStrategy);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceOf('\BricksAssetTest\Mock\PublishStrategy',$as->getPublishStrategy());
		$this->assertInstanceOf('\BricksAssetTest\Mock\PublishStrategy',$module->getPublishStrategy());
		$module->setPublishStrategy($publishStrategy);
		$as->setPublishStrategy($oldPublishStrategy);
		$this->assertInstanceOf('\Bricks\Asset\PublishStrategy\CopyStrategy',$as->getPublishStrategy());
		$this->assertInstanceOf('\BricksAssetTest\Mock\PublishStrategy',$module->getPublishStrategy());
		$module->setPublishStrategy($oldPublishStrategy,true);
		$as->setPublishStrategy($oldPublishStrategy);
	}
	
	public function testConfigRemoveStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$removeStrategy = new BricksAssetTest\Mock\RemoveStrategy();
		$oldRemoveStrategy = $as->getRemoveStrategy();
		$as->setRemoveStrategy($removeStrategy);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceOf('\BricksAssetTest\Mock\RemoveStrategy',$as->getRemoveStrategy());
		$this->assertInstanceOf('\BricksAssetTest\Mock\RemoveStrategy',$module->getRemoveStrategy());
		$module->setRemoveStrategy($removeStrategy);
		$as->setRemoveStrategy($oldRemoveStrategy);
		$this->assertInstanceOf('\Bricks\Asset\RemoveStrategy\RemoveStrategy',$as->getRemoveStrategy());
		$this->assertInstanceOf('\BricksAssetTest\Mock\RemoveStrategy',$module->getRemoveStrategy());
		$module->setRemoveStrategy($oldRemoveStrategy,true);
		$as->setRemoveStrategy($oldRemoveStrategy);
	}
	
	public function testConfigLessStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$lessStrategy = new BricksAssetTest\Mock\LessStrategy();
		$oldLessStrategy = $as->getLessStrategy();
		$as->setLessStrategy($lessStrategy);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceOf('\BricksAssetTest\Mock\LessStrategy',$as->getLessStrategy());
		$this->assertInstanceOf('\BricksAssetTest\Mock\LessStrategy',$module->getLessStrategy());
		$module->setLessStrategy($lessStrategy);
		$as->setLessStrategy($oldLessStrategy);
		$this->assertInstanceOf('\Bricks\Asset\LessStrategy\OyejorgeLessphpStrategy',$as->getLessStrategy());
		$this->assertInstanceOf('\BricksAssetTest\Mock\LessStrategy',$module->getLessStrategy());
		$module->setLessStrategy($oldLessStrategy,true);
		$as->setLessStrategy($oldLessStrategy);
	}
	
	public function testConfigScssStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$scssStrategy = new BricksAssetTest\Mock\ScssStrategy();
		$oldScssStrategy = $as->getScssStrategy();
		$as->setScssStrategy($scssStrategy);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceOf('\BricksAssetTest\Mock\ScssStrategy',$as->getScssStrategy());
		$this->assertInstanceOf('\BricksAssetTest\Mock\ScssStrategy',$module->getScssStrategy());
		$module->setScssStrategy($scssStrategy);
		$as->setScssStrategy($oldScssStrategy);
		$this->assertInstanceOf('\Bricks\Asset\ScssStrategy\LeafoScssphpStrategy',$as->getScssStrategy());
		$this->assertInstanceOf('\BricksAssetTest\Mock\ScssStrategy',$module->getScssStrategy());
		$module->setScssStrategy($oldScssStrategy,true);
		$as->setScssStrategy($oldScssStrategy);
	}
	
	public function testConfigMinifyCssStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$minifyCssStrategy = new BricksAssetTest\Mock\MinifyCssStrategy();
		$oldMinifyCssStrategy = $as->getMinifyCssStrategy();
		$as->setMinifyCssStrategy($minifyCssStrategy);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceOf('\BricksAssetTest\Mock\MinifyCssStrategy',$as->getMinifyCssStrategy());
		$this->assertInstanceOf('\BricksAssetTest\Mock\MinifyCssStrategy',$module->getMinifyCssStrategy());
		$module->setMinifyCssStrategy($minifyCssStrategy);
		$as->setMinifyCssStrategy($oldMinifyCssStrategy);
		$this->assertInstanceOf('\Bricks\Asset\MinifyCssStrategy\MrclayMinifyStrategy',$as->getMinifyCssStrategy());
		$this->assertInstanceOf('\BricksAssetTest\Mock\MinifyCssStrategy',$module->getMinifyCssStrategy());
		$module->setMinifyCssStrategy($oldMinifyCssStrategy,true);
		$as->setMinifyCssStrategy($oldMinifyCssStrategy);
	}
	
	public function testConfigMinifyJsStrategy(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$minifyJsStrategy = new BricksAssetTest\Mock\MinifyJsStrategy();
		$oldMinifyJsStrategy = $as->getMinifyJsStrategy();
		$as->setMinifyJsStrategy($minifyJsStrategy);
		$module = $as->getModule('BricksAsset');
		$this->assertInstanceOf('\BricksAssetTest\Mock\MinifyJsStrategy',$as->getMinifyJsStrategy());
		$this->assertInstanceOf('\BricksAssetTest\Mock\MinifyJsStrategy',$module->getMinifyJsStrategy());
		$module->setMinifyJsStrategy($minifyJsStrategy);
		$as->setMinifyJsStrategy($oldMinifyJsStrategy);
		$this->assertInstanceOf('\Bricks\Asset\MinifyJsStrategy\MrclayMinifyStrategy',$as->getMinifyJsStrategy());
		$this->assertInstanceOf('\BricksAssetTest\Mock\MinifyJsStrategy',$module->getMinifyJsStrategy());
		$module->setMinifyJsStrategy($oldMinifyJsStrategy,true);
		$as->setMinifyJsStrategy($oldMinifyJsStrategy);
	}
	
	public function testConfigWwwRootPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$path = realpath('./httpdocs2');
		$oldPath = $as->getWwwRootPath();		
		$as->setWwwRootPath($path);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals($path,$as->getWwwRootPath());
		$this->assertEquals($path,$module->getWwwRootPath());
		$module->setWwwRootPath($path);
		$as->setWwwRootPath($oldPath);
		$this->assertEquals(realpath('./httpdocs'),$as->getWwwRootPath());
		$this->assertEquals(realpath('./httpdocs2'),$module->getWwwRootPath());
		$module->setWwwRootPath($oldPath,true);
		$as->setWwwRootPath($oldPath);
	}
	
	public function testConfigHttpAssetsPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$path = 'module2';
		$oldPath = $as->getHttpAssetsPath();
		$as->setHttpAssetsPath($path);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals($path,$as->getHttpAssetsPath());
		$this->assertEquals($path,$module->getHttpAssetsPath());
		$module->setHttpAssetsPath($path);
		$as->setHttpAssetsPath($oldPath);
		$this->assertEquals('module',$as->getHttpAssetsPath());
		$this->assertEquals('module2',$module->getHttpAssetsPath());
		$module->setHttpAssetsPath($oldPath,true);
		$as->setHttpAssetsPath($oldPath);
	}
	
	public function testConfigModuleAssetsPath(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$module = $as->getModule('BricksAsset');
		$path = realpath('./public2');
		$oldPath = $module->getModuleAssetsPath();
		$module->setModuleAssetsPath($path);
		$this->assertEquals($path,$module->getModuleAssetsPath());
		$module->setModuleAssetsPath($oldPath);
	}
	
	public function testConfigAutoPublish(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$autoPublish = true;
		$oldAutoPublish = $as->getAutoPublish();
		$as->setAutoPublish($autoPublish);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals($autoPublish,$as->getAutoPublish());
		$this->assertEquals($autoPublish,$module->getAutoPublish());
		$module->setAutoPublish($autoPublish);
		$as->setAutoPublish($oldAutoPublish);
		$this->assertEquals(false,$as->getAutoPublish());
		$this->assertEquals(true,$module->getAutoPublish());
		$module->setAutoPublish($oldAutoPublish,true);
		$as->setAutoPublish($oldAutoPublish);
	}
	
	public function testConfigAutoOptimize(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$autoOptimize = true;
		$oldAutoOptimize = $as->getAutoOptimize();
		$as->setAutoOptimize($autoOptimize);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals($autoOptimize,$as->getAutoOptimize());
		$this->assertEquals($autoOptimize,$module->getAutoOptimize());
		$module->setAutoOptimize($autoOptimize);
		$as->setAutoOptimize($oldAutoOptimize);
		$this->assertEquals(false,$as->getAutoOptimize());
		$this->assertEquals(true,$module->getAutoOptimize());
		$module->setAutoOptimize($oldAutoOptimize,true);
		$as->setAutoOptimize($oldAutoOptimize);
	}
	
	public function testConfigLessSupport(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$lessSupport = true;
		$oldLessSupport = $as->getLessSupport();
		$as->setLessSupport($lessSupport);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals($lessSupport,$as->getLessSupport());
		$this->assertEquals($lessSupport,$module->getLessSupport());
		$module->setLessSupport($lessSupport);
		$as->setLessSupport($oldLessSupport);
		$this->assertEquals(false,$as->getLessSupport());
		$this->assertEquals(true,$module->getLessSupport());
		$module->setLessSupport($oldLessSupport,true);
		$as->setLessSupport($oldLessSupport);
	}
	
	public function testConfigScssSupport(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$scssSupport = true;
		$oldScssSupport = $as->getScssSupport();
		$as->setScssSupport($scssSupport);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals($scssSupport,$as->getScssSupport());
		$this->assertEquals($scssSupport,$module->getScssSupport());
		$module->setScssSupport($scssSupport);
		$as->setScssSupport($oldScssSupport);
		$this->assertEquals(false,$as->getScssSupport());
		$this->assertEquals(true,$module->getScssSupport());
		$module->setScssSupport($oldScssSupport,true);
		$as->setScssSupport($oldScssSupport);
	}
	
	public function testConfigMinifyCssSupport(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$minifyCssSupport = true;
		$oldMinifyCssSupport = $as->getMinifyCssSupport();
		$as->setMinifyCssSupport($minifyCssSupport);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals($minifyCssSupport,$as->getMinifyCssSupport());
		$this->assertEquals($minifyCssSupport,$module->getMinifyCssSupport());
		$module->setMinifyCssSupport($minifyCssSupport);
		$as->setMinifyCssSupport($oldMinifyCssSupport);
		$this->assertEquals(false,$as->getMinifyCssSupport());
		$this->assertEquals(true,$module->getMinifyCssSupport());
		$module->setMinifyCssSupport($oldMinifyCssSupport,true);
		$as->setMinifyCssSupport($oldMinifyCssSupport);
	}
	
	public function testConfigMinifyJsSupport(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$minifyJsSupport = true;
		$oldMinifyJsSupport = $as->getMinifyJsSupport();
		$as->setMinifyJsSupport($minifyJsSupport);
		$module = $as->getModule('BricksAsset');
		$this->assertEquals($minifyJsSupport,$as->getMinifyJsSupport());
		$this->assertEquals($minifyJsSupport,$module->getMinifyJsSupport());
		$module->setMinifyJsSupport($minifyJsSupport);
		$as->setMinifyJsSupport($oldMinifyJsSupport);
		$this->assertEquals(false,$as->getMinifyJsSupport());
		$this->assertEquals(true,$module->getMinifyJsSupport());
		$module->setMinifyJsSupport($oldMinifyJsSupport,true);
		$as->setMinifyJsSupport($oldMinifyJsSupport);
	}
	
	public function testConfigExclude(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$this->assertInstanceOf('\Bricks\Asset\AssetService',$as);
		$exclude = array('^*.css$');
		$module = $as->getModule('BricksAsset');
		$oldExclude = $as->getExclude();		
		$as->setExclude($exclude);
		$this->assertEquals($exclude,$as->getExclude());
		$this->assertEquals($exclude,$module->getExclude());
		$module->setExclude(array('test'));
		$as->setExclude(array());
		$this->assertEquals(array(),$as->getExclude());		
		$this->assertEquals(array('test'),$module->getExclude());
		$module->setExclude($oldExclude,true);
		$as->setExclude($oldExclude);
	}
	
	public function testPublish(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$module = $as->getModule('BricksAsset');		
		$httpdir = realpath($module->getWwwRootPath().'/'.$module->getHttpAssetsPath()).'/'.$module->getModuleName();
		
		$module->setLessSupport(true);
		$module->setScssSupport(true);
		
		$as->publish('BricksAsset');
		
		$this->assertFileExists($httpdir);		
		$this->assertFileExists($httpdir.'/css');
		$this->assertFileExists($httpdir.'/css/test.css');
		$this->assertFileExists($httpdir.'/css/test.less');
		$this->assertFileExists($httpdir.'/css/test.less.css');
		$this->assertFileExists($httpdir.'/css/test.scss');
		$this->assertFileExists($httpdir.'/css/test.scss.css');
		$this->assertFileExists($httpdir.'/css/test.sass');
		$this->assertFileExists($httpdir.'/css/test.sass.css');
		$this->assertFileExists($httpdir.'/js/test.js');
	}
	
	/*
	 * @depends testPublish
	 * @depends testRemove
	 */
	public function testPublishExclude(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$module = $as->getModule('BricksAsset');
		$httpdir = realpath($module->getWwwRootPath().'/'.$module->getHttpAssetsPath()).'/'.$module->getModuleName();
	
		$as->remove('BricksAsset');
		
		$module->setExclude(array('*/js/*'));
		
		$as->publish('BricksAsset');
	
		$module->setExclude(array());
		
		$this->assertFileExists($httpdir);		
		$this->assertEquals(false,file_exists($httpdir.'/js/test.js'));
	}
	
	/**
	 * @depends testPublish
	 */
	public function testOptimize(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$module = $as->getModule('BricksAsset');
		$httpdir = realpath($module->getWwwRootPath().'/'.$module->getHttpAssetsPath().'/BricksAsset');
		
		$module->setMinifyCssSupport(true);
		$module->setMinifyJsSupport(true);
		
		$as->publish('BricksAsset');		
		$as->optimize('BricksAsset');				
		
		$this->assertFileExists($httpdir.'/css/test.less.min.css');		
		$this->assertFileExists($httpdir.'/css/test.scss.min.css');		
		$this->assertFileExists($httpdir.'/css/test.sass.min.css');
		$this->assertFileExists($httpdir.'/js/test.min.js');
	}
	
	/**
	 * @depends testPublish
	 * @depends testOptimize
	 */
	public function testRemove(){
		$as = Bootstrap::getServiceManager()->get('Bricks\Asset');
		$as->publish('BricksAsset');
		$module = $as->getModule('BricksAsset');				
		$httpdir = realpath($module->getWwwRootPath().'/'.$module->getHttpAssetsPath().'/BricksAsset');
		$module->setExclude(array('/js\/test.js$/'));		
		$as->remove('BricksAsset');
		$this->assertFileExists($httpdir.'/js/test.js');
		$this->assertEquals(false,file_exists($httpdir.'/css'));
		$module->setExclude(array(),true);
	}
	
}