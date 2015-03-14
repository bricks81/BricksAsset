<?php

namespace BricksAssetTest\Mock;

use Bricks\Asset\ClassLoader\ClassLoaderInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClassLoader implements ClassLoaderInterface {
	
protected static $instance;
	
	protected $di;
	
	protected $sl;
	
	protected function __construct(ServiceLocatorInterface $sl){
		$this->di = $sl->get('Di');
		$this->sl = $sl;
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
	
	public function getServiceLocator(){
		return $this->sl;
	}
	
}