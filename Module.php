<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace BricksAsset;

use Bricks\View\Helper\HeadLink;

use Zend\Di\Di;

use Zend\Mvc\MvcEvent;

class Module {
	
	public function getConfig(){
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig(){
        return array(
        	'Zend\Loader\ClassMapAutoloader' => array(
        		__DIR__ . '/autoload_classmap.php',
        	),      	
        );
    }
    
    public function onBootstrap(MvcEvent $e){
    	
    	$e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_DISPATCH,function(MvcEvent $e){
    		$sm = $e->getApplication()->getServiceManager();    		
    		$as = $sm->get('BricksAsset');
    		$as->autoPublish();
    		$as->autoOptimize();    										
    	});
    	
    	$e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_RENDER,function(MvcEvent $e){
    		$e->getApplication()->getServiceManager()->get('ViewHelperManager')->get('HeadLink')
    			->appendStylesheet('bootstrap/css/bootstrap.min.css');    			
    		$e->getApplication()->getServiceManager()->get('ViewHelperManager')->get('HeadScript')
    			->appendFile('bootstrap/js/bootstrap.min.js');
    	});
		
    }
    
}
