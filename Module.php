<?php

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
    	
		$hl = $e->getApplication()->getServiceManager()->get('ViewHelperManager')->get('HeadLink');
		if(method_exists($hl,'setServiceManager')){
			$hl->setServiceManager($e->getApplication()->getServiceManager());
		}
		$hs = $e->getApplication()->getServiceManager()->get('ViewHelperManager')->get('HeadScript');
		if(method_exists($hs,'setServiceManager')){
			$hs->setServiceManager($e->getApplication()->getServiceManager());
		}

		$cfg = $e->getApplication()->getServiceManager()->get('Config');
		if(isset($cfg['Bricks']['BricksAsset'])){
			$assetsConfig = $cfg['Bricks']['BricksAsset'];
			$as = $e->getApplication()->getServiceManager()->get('Bricks\AssetService');
			if(isset($assetsConfig['autoPublish'])&&$assetsConfig['autoPublish']){
				$as->publish();
			}
			if(isset($assetsConfig['autoOptimize'])&&$assetsConfig['autoOptimize']){
				$as->optimize();
			}
		}		
		
    }
    
}
