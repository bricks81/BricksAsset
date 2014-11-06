<?php

namespace BricksAssetTest\Mock;

use Bricks\AssetService\ClassLoader\ClassLoaderInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClassLoader implements ClassLoaderInterface {
	
protected static $instance;
	
	protected $di;
	
	protected function __construct(ServiceLocatorInterface $sl){
		$this->di = $sl->get('Di');
	}
	
	public static function getInstance(ServiceLocatorInterface $sl=null){
		if(null==self::$instance){
			self::$instance = new self($sl);
		}
		return self::$instance;
	}
	
	public function get($class,array $params=array()){
		return $this->di->get($class,$params);
	}
	
}