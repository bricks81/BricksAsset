<?php

namespace Bricks\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\HeadLink AS ZFHeadLink;
use \stdClass;

class HeadLink extends ZFHeadLink {

	protected $sm;
	
	protected $moduleNames = array();
	
	/**
	 * @return ServiceManager
	 */
	public function getServiceManager(){
		return $this->sm;
	}

	/**
	 * @param ServiceManager $sm	 
	 */
	public function setServiceManager(ServiceManager $sm){
		$this->sm = $sm;		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\View\Helper\HeadLink::itemToString()
	 */
	public function itemToString(stdClass $item){
		$cfg = $this->getServiceManager()->get('Config');
		if(!isset($cfg['Bricks']['BricksAsset']['http_assets_path'])){
			return parent::itemToString($item);
		}
		$assetsCfg = $cfg['Bricks']['BricksAsset'];
		
		if(!count($this->moduleNames)){
			$mm = self::getServiceManager()->get('ModuleManager');
			$modules = $mm->getLoadedModules();
			$moduleNames = array();
			foreach($modules AS $module){
				$class = get_class($module);
				$moduleNames[] = substr($class,0,strpos($class,'\\'));
			}
			$this->moduleNames = $moduleNames;
		}
		
		$moduleName = '';
		foreach($this->moduleNames AS $name){		
			if(false!==strpos($item->href,'/'.$name.'/')){
				$moduleName = $name;
				break;
			}
		}		
		if(empty($moduleName)){
			return parent::itemToString($item);
		}
		
		$lessSupport = false;
		if(isset($assetsCfg['module_specific'][$moduleName]['lessSupport'])){
			$lessSupport = $assetsCfg['module_specific'][$moduleName]['lessSupport'];
		} elseif(isset($assetsCfg['lessSupport'])){
			$lessSupport = $assetsCfg['lessSupport'];
		}
		
		$scssSupport = false;
		if(isset($assetsCfg['module_specific'][$moduleName]['scssSupport'])){
			$scssSupport = $assetsCfg['module_specific'][$moduleName]['scssSupport'];
		} elseif(isset($assetsCfg['scssSupport'])){
			$scssSupport = $assetsCfg['scssSupport'];
		}
		
		$minifySupport = false;
		if(isset($assetsCfg['module_specific'][$moduleName]['minifyCssSupport'])){
			$minifySupport = $assetsCfg['module_specific'][$moduleName]['minifyCssSupport'];
		} elseif(isset($assetsCfg['minifyCssSupport'])){
			$minifySupport = $assetsCfg['minifyCssSupport'];
		}
		
		if(isset($assetsCfg['module_specific'][$moduleName]['http_assets_path'])){
			$http_assets_path = $assetsCfg['module_specific'][$moduleName]['http_assets_path'];			
		} elseif(isset($assetsCfg['http_assets_path'])){
			$http_assets_path = $assetsCfg['http_assets_path'];
		}
		if(!isset($http_assets_path)){
			return parent::itemToString($item);
		}
		
		$href = $item->href;
		if(true==$lessSupport){
			if(substr($item->href,-5)=='.less'){
				$href .= '.css';
			}
		}
		if(true==$scssSupport){
			if(substr($item->href,-5)=='.scss'||substr($item->href,-5)=='.sass'){
				$href .= '.css';
			}
		}
		$file = $assetsCfg['wwwroot_path'].'/'.$href;
		if(file_exists($file)){
			$item->href = $href;
		}
		
		$href = $item->href;
		if(true==$minifySupport){			
			if(substr($href,-4)=='.css'&&substr($href,-7)!=='.min.css'){
				$href = substr($href,0,strlen($href)-4).'.min.css';								
			}
		}
		$file = $assetsCfg['wwwroot_path'].'/'.$href;
		if(file_exists($file)){
			$item->href = $href;
		}		
		
		return parent::itemToString($item);
	}	
	
}