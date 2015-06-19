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
use Zend\View\Helper\InlineScript AS ZFInlineScript;
use \stdClass;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Bricks\Asset\Asset;

class InlineScriptDelegator extends ZFInlineScript implements DelegatorFactoryInterface {

	/**
	 * @var Asset
	 */
	protected $as;
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\DelegatorFactoryInterface::createDelegatorWithName()
	 */
	public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator,$name,$requestedName,$callback){
		$headScript = new self();
		$headScript->setAsset($serviceLocator->getServiceLocator()->get('BricksAsset'));
		return $headScript;
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
	 * @see \Zend\View\Helper\HeadScript::itemToString()
	 */
	public function itemToString($item, $indent, $escapeStart, $escapeEnd){
		if(!isset($item->attributes['src'])){
			return parent::itemToString($item,$indent,$escapeStart,$escapeEnd);
		}
		$moduleName = '';
		foreach($this->getAsset()->getLoadedModules() AS $name){		
			if(substr($item->attributes['src'],0,strlen($name))==$name){
				$moduleName = $name;
				break;
			}
		}		
		if(empty($moduleName)){
			return parent::itemToString($item,$indent,$escapeStart,$escapeEnd);
		}
		
		$cfg = $this->getAsset()->getConfig()->getArray($moduleName);
		$minifyJsSupport = $cfg['minifyJsSupport'];
		
		$http_assets_path = $cfg['httpAssetsPath'];
		$wwwroot_path = $cfg['wwwrootPath'];
		
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