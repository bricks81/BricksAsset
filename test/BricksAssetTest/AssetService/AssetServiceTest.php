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
			'autoPublish' => false,
			'autoOptimize' => false,
			'minifyCssSupport' => true,
			'minifyJsSupport' => true,
			'lessSupport' => true,
			'scssSupport' => true,
			'publishAdapter' => 'BricksAssetTest\Mocking\AssetService\PublishAdapter\MockAdapter',
			'module_assets_path' => 'test/public',
			'wwwroot_path' => realpath('./httpdocs'),
			'http_assets_path' => 'module',			
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
	
	public function testRemovePublishAndOptimize(){
		$as = $this->getAssetService();
		$cfg = $as->getConfig();
		$cfg['publishAdapter'] = 'Bricks\AssetService\PublishAdapter\CopyAdapter';
		$module_path = realpath('./httpdocs').'/module/BricksAsset';
		
		$as->setConfig($cfg);
		if(file_exists($module_path)){
			$as->remove(array('BricksAsset'));
		}
		$this->assertTrue(!file_exists($module_path));
		$as->publish(array('BricksAsset'));
		$as->optimize(array('BricksAsset'));
		
		$this->assertTrue(file_exists($module_path.'/css'));
		$this->assertTrue(file_exists($module_path.'/css/test.css'));
		$this->assertTrue(file_exists($module_path.'/css/test.less'));
		$this->assertTrue(file_exists($module_path.'/css/test.less.css'));
		$this->assertTrue(file_exists($module_path.'/css/test.less.min.css'));
		$this->assertTrue(file_exists($module_path.'/css/test.scss'));
		$this->assertTrue(file_exists($module_path.'/css/test.scss.css'));
		$this->assertTrue(file_exists($module_path.'/css/test.scss.min.css'));
		$this->assertTrue(file_exists($module_path.'/css/test.sass'));
		$this->assertTrue(file_exists($module_path.'/css/test.sass.css'));
		$this->assertTrue(file_exists($module_path.'/css/test.sass.min.css'));
		$this->assertTrue(file_exists($module_path.'/js/test.js'));
		$this->assertTrue(file_exists($module_path.'/js/test.min.js'));
	}
	
}