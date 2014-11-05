<?php

namespace Bricks\AssetService\ClassLoader;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * The class loader who will load the neccessary classes
 */
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
	
	public static function get($class,array $params=array()){
		return $this->di->get($class,$params);
	}
	
}