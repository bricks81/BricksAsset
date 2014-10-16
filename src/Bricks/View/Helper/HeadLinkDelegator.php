<?php 

namespace Bricks\View\Helper;

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HeadLinkDelegator implements DelegatorFactoryInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\DelegatorFactoryInterface::createDelegatorWithName()
	 */
    public function createDelegatorWithName(ServiceLocatorInterface $sl,$name,$requestedName,$callback){
        $headLink = $callback();
		$headLink->setServiceManager($sl->getServiceLocator());        
        return $headLink;
    }
}