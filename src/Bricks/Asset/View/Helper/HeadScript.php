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

namespace Bricks\Asset\View\Helper;

use Zend\EventManager\Event;
use Bricks\Plugin\Extender;
use Bricks\Plugin\Extender\VisitorInterface;
use Bricks\ClassLoader\ClassLoader;

/**
 * This class will hold the autoloader with it's classmap
 * in front of the internal autoloading stack.
 */
class HeadScript implements VisitorInterface {
	
	/**
	 * @var Extender
	 */
	protected $extender;
	
	/**
	 * @var \Bricks\ClassLoader\ClassLoader
	 */
	protected $classLoader;
	
	/**
	 * @param Extender $extender
	 */
	public function __construct(Extender $extender=null){
		$this->setExtender($extender);		
	}
	
	/**
	 * @param Extender $extender
	 */
	public function setExtender(Extender $extender=null){
		$this->extender = $extender;
	}
	
	/**
	 * @return \Bricks\Plugin\Extender
	 */
	public function getExtender(){
		return $this->extender;
	}
	
	/**
	 * @param ClassLoader $classLoader
	 */
	public function setClassLoader(ClassLoader $classLoader){
		$this->classLoader = $classLoader;
	}
	
	/**
	 * @param \Bricks\ClassLoader\ClassLoader
	 */
	public function getClassLoader(){
		return $this->classLoader;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Plugin\Extender\VisitorInterface::extend()
	 */
	public function extend(){
		$this->getExtender()->eventize('Zend\View\Helper','HeadScript','itemToString');
	}
	
	/**
	 * @param Event $event
	 */
	public function postItemToString(Event $event){		
		$item = $event->getParam('item');		
		if(!isset($item->attributes['src'])){
			return;
		}
		
		$asset = $this->getClassLoader()->getServiceLocator()->get('BricksAsset');
		
		$moduleName = '';
		foreach($asset->getLoadedModules() AS $name){			
			if(substr($item->attributes['src'],0,strlen($name))==$name){
				$moduleName = $name;
				break;
			}
		}		
		if(empty($moduleName)){
			return;
		}
		
		$cfg = $asset->getConfig()->getArray($moduleName);
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
		
		$indent = $event->getParam('indent');
		$escapeStart = $event->getParam('escapeStart');
		$escapeEnd = $event->getParam('escapeEnd');
		return $event->getTarget()->itemToString($item,$indent,$escapeStart,$escapeEnd);
	}
	
}