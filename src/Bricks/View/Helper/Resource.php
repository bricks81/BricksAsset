<?php

namespace Bricks\View\Helper;

use Zend\Form\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Resource extends AbstractHelper implements ServiceLocatorAwareInterface {
	
	protected $serviceLocator;
	
	public function setServiceLocator(ServiceLocatorInterface $sl){
		$this->serviceLocator = $sl;
	}
	
	public function getServiceLocator(){
		return $this->serviceLocator;
	}
	
	public function __invoke($path){
		$as = $this->getServiceLocator()->getServiceLocator()->get('Bricks\AssetService');
		$parts = explode('/',$path);
		if($as->hasModule($parts[0])){
			$module = $as->getModule($parts[0]);
			return $module->getHttpAssetsPath().'/'.implode('/',$parts);			
		}
		return $path;
	}
	
}