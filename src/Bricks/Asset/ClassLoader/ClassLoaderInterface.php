<?php

namespace Bricks\Asset\ClassLoader;

use Zend\ServiceManager\ServiceLocatorInterface;

interface ClassLoaderInterface {

	/**
	 * @param ServiceLocatorInterface $sl
	 * @return ClassLoaderInterface
	 */
	public static function getInstance(ServiceLocatorInterface $sl=null);
	
	/**
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator();
	
}