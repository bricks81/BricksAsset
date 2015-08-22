<?php

/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * The MIT License (MIT)
 * Copyright (c) 2015 bricks-cms.org
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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