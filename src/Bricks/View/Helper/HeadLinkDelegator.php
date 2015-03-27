<?php

namespace Bricks\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\HeadLink AS ZFHeadLink;
use \stdClass;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Bricks\Asset\AssetService;

class HeadLinkDelegator extends ZFHeadLink implements DelegatorFactoryInterface {

	protected $as;
	
	public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator,$name,$requestedName,$callback){
		$headLink = new self();		
		$headLink->setAssetService($serviceLocator->getServiceLocator()->get('Bricks.Asset.AssetService'));
		return $headLink;	 		
	}
	
	public function setAssetService(AssetService $as){
		$this->as = $as;
	}
	
	public function getAssetService(){
		return $this->as;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\View\Helper\HeadLink::itemToString()
	 */
	public function itemToString(stdClass $item){
		$moduleName = '';
		foreach($this->getAssetService()->getLoadedModules() AS $name){		
			if(substr($item->href,0,strlen($name))==$name){
				$moduleName = $name;
				break;
			}
		}		
		if(empty($moduleName)){
			return parent::itemToString($item);
		}
		
		$lessSupport = $this->getAssetService()->getModule($moduleName)->getLessSupport();
		$scssSupport = $this->getAssetService()->getModule($moduleName)->getScssSupport();
		$minifyCssSupport = $this->getAssetService()->getMinifyCssSupport($moduleName);		

		$http_assets_path = $this->getAssetService()->getHttpAssetsPath($moduleName);
		$wwwroot_path = $this->getAssetService()->getWwwrootPath($moduleName);
		
		// we add the assets path if it's missing and the file exists
		if(substr($item->href,0,strlen($http_assets_path))!=$http_assets_path){
			$file = realpath($wwwroot_path).'/'.$http_assets_path.'/'.$item->href;
			if(file_exists($file)){
				$item->href = $http_assets_path.'/'.$item->href;
			}
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
		$file = $wwwroot_path.'/'.$href;
		if(file_exists($file)){
			$item->href = $href;
		}
		
		$href = $item->href;
		if(true==$minifyCssSupport){			
			if(substr($href,-4)=='.css'&&substr($href,-7)!=='.min.css'){
				$href = substr($href,0,strlen($href)-4).'.min.css';								
			}
		}
		$file = $wwwroot_path.'/'.$href;
		if(file_exists($file)){
			$item->href = $href;
		}		
		
		return parent::itemToString($item);
	}	
	
}