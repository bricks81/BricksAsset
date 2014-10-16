<?php 

namespace Bricks\View\Helper;

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HeadScriptDelegator implements DelegatorFactoryInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\DelegatorFactoryInterface::createDelegatorWithName()
	 */
    public function createDelegatorWithName(ServiceLocatorInterface $sl,$name,$requestedName,$callback){
        $headScript = $callback();
		$headScript->setServiceManager($sl->getServiceLocator());        
        return $headScript;
    }
}