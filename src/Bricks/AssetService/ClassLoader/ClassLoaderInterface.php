<?php

namespace Bricks\AssetService\ClassLoader;

use Zend\ServiceManager\ServiceLocatorInterface;
interface ClassLoaderInterface {

	/**
	 * @param ServiceLocatorInterface $sl
	 * @return ClassLoaderInterface
	 */
	public static function getInstance(ServiceLocatorInterface $sl=null);
	
	/**
	 * @param string $class
	 * @param array $params
	 * @return object
	 */
	public function get($class,array $params=array());
	
	/**
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator();
	
}