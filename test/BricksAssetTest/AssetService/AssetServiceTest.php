<?php

namespace BricksAssetTest\AssetService;

use Bricks\File\Directory;

use BricksAssetTest\Mocking\AssetService\PublishAdapter\MockAdapter;
use Bricks\AssetService\AssetService;
use Zend\Log\Logger;
use Zend\Log\Writer\Mock;
use PHPUnit_Framework_TestCase;
use BricksAssetTest\Bootstrap;

class AssetServiceTest extends PHPUnit_Framework_TestCase {
	
	protected $as;
	
	public function setUp(){
		$this->as = Bootstrap::getServiceManager()->get('Bricks\AssetService');
		$this->as->setConfig(array(
			'adapter' => 'BricksAssetTest\Mocking\AssetService\PublishAdapter\MockAdapter',
			'module_assets_path' => './test/public',
			'wwwroot_path' => './httpdocs',
			'http_assets_path' => 'module',
			'http_cache_path' => 'module/0cache',
		));			
	}
	
	/**
	 * @return AssetService
	 */
	public function getAssetService(){
		return $this->as;
	}
	
	public function testInstantiation(){
		$this->assertTrue(Bootstrap::getServiceManager()->get('Bricks\AssetService') instanceof AssetService);		
	}
	
	public function testTryRun(){
		$as = $this->getAssetService();
		$writer = new Mock();
		$logger = new Logger();
		$logger->addWriter($writer);
		MockAdapter::setLogger($logger);
		$as->publish(array('BricksAsset'));		
		
		$module = Bootstrap::getServiceManager()->get('ModuleManager')->getModule('BricksAsset');
		$class = get_class($module);
		$ref = new \ReflectionClass($class);
		$dir = dirname($ref->getFileName());
		
		$cfg = $as->getConfig();		
		$source = $writer->events[0]['message'];
		$target = $writer->events[1]['message'];
		$module_path = realpath($dir.'/'.$cfg['module_assets_path']);		
		$this->assertTrue($source==$module_path);
		
		$assets_path = realpath($cfg['wwwroot_path']).'/'.$cfg['http_assets_path'].'/BricksAsset';
		$this->assertTrue($target==$assets_path);
	}
	
	public function testPublish(){
		$as = $this->getAssetService();
		$cfg = $as->getConfig();
		$cfg['adapter'] = 'Bricks\AssetService\PublishAdapter\CopyAdapter';
		$as->setConfig($cfg);
		$as->publish(array('BricksAsset'));
		$dir = './httpdocs/module';
		$modul = $dir.'/bricksasset';
		$cache = $dir.'/0cache';
		$mcache = $cache.'/bricksasset';
		$this->assertTrue(file_exists($dir));
		$this->assertTrue(file_exists($modul.'/css'));
		$this->assertTrue(file_exists($modul.'/css/test.css'));
		$this->assertTrue(file_exists($modul.'/css/test.less'));
		$this->assertTrue(file_exists($cache));
		$this->assertTrue(file_exists($mcache));
		$this->assertTrue(file_exists($mcache.'/css'));
		$this->assertTrue(file_exists($mcache.'/css/test.less.css'));				
	}
	
}