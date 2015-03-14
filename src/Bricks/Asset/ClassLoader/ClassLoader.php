<?php

namespace Bricks\Asset\ClassLoader;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * The class loader who will load the neccessary classes
 */
class ClassLoader implements ClassLoaderInterface {
	
	protected static $instance;
	
	protected $sl;
	
	protected function __construct(ServiceLocatorInterface $sl){
		$this->sl = $sl;
	}
	
	public static function getInstance(ServiceLocatorInterface $sl=null){
		if(null==self::$instance){
			self::$instance = new self($sl);
		}
		return self::$instance;
	}
	
	public function getServiceLocator(){
		return $this->sl;
	}
	
}