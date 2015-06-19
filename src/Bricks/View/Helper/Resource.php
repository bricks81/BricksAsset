<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
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
		$as = $this->getServiceLocator()->getServiceLocator()->get('BricksAsset');
		$parts = explode('/',$path);
		if($as->hasAsset($parts[0])){			
			return $as->getConfig()->get('httpAssetsPath',$parts[0]).'/'.implode('/',$parts);			
		}
		return $path;
	}
	
}