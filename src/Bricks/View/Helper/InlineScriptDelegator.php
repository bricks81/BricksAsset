<?php

namespace Bricks\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\InlineScript AS ZFInlineScript;
use \stdClass;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Bricks\Asset\AssetService;

class InlineScriptDelegator extends ZFInlineScript implements DelegatorFactoryInterface {

	protected $as;
	
	public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator,$name,$requestedName,$callback){
		$headScript = new self();
		$headScript->setAssetService($serviceLocator->getServiceLocator()->get('Bricks\Asset'));
		return $headScript;
	}
	
	public function setAssetService(AssetService $as){
		$this->as = $as;
	}
	
	public function getAssetService(){
		return $this->as;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\View\Helper\HeadScript::itemToString()
	 */
	public function itemToString($item, $indent, $escapeStart, $escapeEnd){
		if(!isset($item->attributes['src'])){
			return parent::itemToString($item,$indent,$escapeStart,$escapeEnd);
		}
		$moduleName = '';
		foreach($this->getAssetService()->getLoadedModules() AS $name){		
			if(substr($item->attributes['src'],0,strlen($name))==$name){
				$moduleName = $name;
				break;
			}
		}		
		if(empty($moduleName)){
			return parent::itemToString($item,$indent,$escapeStart,$escapeEnd);
		}
		
		$minifyJsSupport = $this->getAssetService()->getModule($moduleName)->getMinifyJsSupport();
		
		$http_assets_path = $this->getAssetService()->getHttpAssetsPath($moduleName);
		$wwwroot_path = $this->getAssetService()->getWwwrootPath($moduleName);
		
		// we add the assets path if it's missing and the file exists
		if(substr($item->attributes['src'],0,strlen($http_assets_path))!=$http_assets_path){
			$file = realpath($wwwroot_path).'/'.$http_assets_path.'/'.$item->attributes['src'];
			if(file_exists($file)){
				$item->attributes['src'] = $http_assets_path.'/'.$item->attributes['src'];
			}
		}
		
		$href = $item->attributes['src'];
		if(true==$minifyJsSupport){
			if(substr($href,-3)=='.js'){			
				$href = substr($href,0,strlen($href)-3).'.min.js';			
			}		
		}
		$file = $wwwroot_path.'/'.$href;
		if(file_exists($file)){
			$item->attributes['src'] = $href;
		}
		
		return parent::itemToString($item,$indent,$escapeStart,$escapeEnd);
	}	
	
}