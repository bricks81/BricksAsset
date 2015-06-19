<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\HeadLink AS ZFHeadLink;
use \stdClass;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Bricks\Asset\Asset;

class HeadLinkDelegator extends ZFHeadLink implements DelegatorFactoryInterface {

	/**
	 * @var Asset
	 */
	protected $as;
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\DelegatorFactoryInterface::createDelegatorWithName()
	 */
	public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator,$name,$requestedName,$callback){
		$headLink = new self();
		$headLink->setAsset($serviceLocator->getServiceLocator()->get('BricksAsset'));
		return $headLink;	 		
	}
	
	/**
	 * @param Asset $as
	 */
	public function setAsset(Asset $as){
		$this->as = $as;
	}
	
	/**
	 * @return \Bricks\Asset\Asset
	 */
	public function getAsset(){
		return $this->as;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\View\Helper\HeadLink::itemToString()
	 */
	public function itemToString(stdClass $item){
		$moduleName = '';
		foreach($this->getAsset()->getLoadedModules() AS $name){		
			if(substr($item->href,0,strlen($name))==$name){
				$moduleName = $name;
				break;
			}
		}		
		if(empty($moduleName)){
			return parent::itemToString($item);
		}
		
		$cfg = $this->getAsset()->getConfig()->getArray($moduleName);
		$lessSupport = $cfg['lessSupport'];
		$scssSupport = $cfg['scssSupport'];
		$minifyCssSupport = $cfg['minifyCssSupport'];		

		$http_assets_path = $cfg['httpAssetsPath'];
		$wwwroot_path = $cfg['wwwRootPath'];
		
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