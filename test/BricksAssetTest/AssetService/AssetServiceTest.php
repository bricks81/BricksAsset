<?php

namespace BricksAssetTest\AssetService;

use BricksAssetTest\Mocking\AssetService\PublishAdapter\MockAdapter;
use PHPUnit_Framework_TestCase;
use BricksAssetTest\Bootstrap;
use Bricks\Di\Di;

class AssetServiceTest extends PHPUnit_Framework_TestCase {
	
	public function testGetInstance(){
		$as = Di::getInstance()->newInstance('Bricks\AssetService\AssetService');
		$this->assertInstanceOf('\Bricks\AssetService\AssetService',$as);
	}
	
	public function testAdapter(){
		$as = Di::getInstance()->newInstance('Bricks\AssetService\AssetService');
		$this->assertInstanceOf('\Bricks\AssetService\Adapter\FileAdapter',$as->getAdapter());
		$adapter = $as->getAdapter();
		$new = Di::getInstance()->newInstance('BricksAssetTest\Mock\BricksAsset\Adapter\AdapterMock');
		$as->setAdapter($new);
		$this->assertInstanceOf('BricksAssetTest\Mock\BricksAsset\Adapter\AdapterMock',$as->getAdapter());		
	}
	
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
	
}