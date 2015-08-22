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
use Zend\View\Helper\HeadScript AS ZFHeadScript;
use \stdClass;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Bricks\Asset\Asset;

class HeadScriptDelegator extends ZFHeadScript implements DelegatorFactoryInterface {

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
		$wwwroot_path = $cfg['wwwRootPath'];
		
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