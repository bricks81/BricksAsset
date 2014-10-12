<?php

namespace Bricks\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\HeadScript AS ZFHeadScript;
use \stdClass;

class HeadScript extends ZFHeadScript {

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
	 * @see \Zend\View\Helper\HeadScript::itemToString()
	 */
	public function itemToString($item, $indent, $escapeStart, $escapeEnd){
		if(!isset($item->attributes['src'])){
			return parent::itemToString($item,$indent,$escapeStart,$escapeEnd);
		}
		$cfg = $this->getServiceManager()->get('Configuration');
		if(!isset($cfg['Bricks']['BricksAsset']['http_assets_path'])){
			return parent::itemToString($item,$indent,$escapeStart,$escapeEnd);
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
			if(substr($item->attributes['src'],0,strlen($name))==$name){
				$moduleName = $name;
				break;
			}
		}		
		if(empty($moduleName)){
			return parent::itemToString($item,$indent,$escapeStart,$escapeEnd);
		}
		
		if(isset($assetsCfg['module_specific'][$moduleName]['minifyJsSupport'])){
			$minifySupport = $assetsCfg['module_specific'][$moduleName]['minifyJsSupport'];
		} elseif(isset($assetsCfg['minifyJsSupport'])){
			$minifySupport = $assetsCfg['minifyJsSupport'];
		}
		
		if(isset($assetsCfg['module_specific'][$moduleName]['http_assets_path'])){
			$http_assets_path = $assetsCfg['module_specific'][$moduleName]['http_assets_path'];
		} elseif(isset($assetsCfg['http_assets_path'])){
			$http_assets_path = $assetsCfg['http_assets_path'];
		}
		if(!isset($http_assets_path)){
			return parent::itemToString($item,$indent,$escapeStart,$escapeEnd);
		}
		
		if(isset($assetsCfg['module_specific'][$moduleName]['wwwroot_path'])){
			$wwwroot_path = $assetsCfg['module_specific'][$moduleName]['wwwroot_path'];
		} elseif(isset($assetsCfg['wwwroot_path'])){
			$wwwroot_path = $assetsCfg['wwwroot_path'];
		}
		if(!isset($wwwroot_path)){
			return parent::itemToString($item);
		}
		
		// we add the assets path if it's missing and the file exists
		if(substr($item->attributes['src'],0,strlen($http_assets_path))!=$http_assets_path){
			$file = realpath($wwwroot_path).'/'.$http_assets_path.'/'.$item->attributes['src'];
			if(file_exists($file)){
				$item->attributes['src'] = $http_assets_path.'/'.$item->attributes['src'];
			}
		}
		
		$href = $item->attributes['src'];
		if(true==$minifySupport){
			if(substr($href,-3)=='.js'){			
				$href = substr($href,0,strlen($href)-3).'.min.js';			
			}		
		}
		$file = $assetsCfg['wwwroot_path'].'/'.$href;
		if(file_exists($file)){
			$item->attributes['src'] = $href;
		}
		
		return parent::itemToString($item,$indent,$escapeStart,$escapeEnd);
	}	
	
}