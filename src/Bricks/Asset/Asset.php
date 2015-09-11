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

namespace Bricks\Asset;

use Bricks\Config\ConfigInterface;
use Bricks\ClassLoader\ClassLoader;

class Asset {
	
	/**
	 * an array containing strings with the names of each module
	 * @var array
	 */
	protected $loadedModules = array();
	
	/**
	 * @var \Bricks\Config\ConfigInterface
	 */
	protected $config = array();
	
	/**
	 * @var \Bricks\ClassLoader\ClassLoader
	 */
	protected $classLoader;
	
	/**
	 *  @var array
	 */
	protected $assets = array();
	
	/**
	 * @param ConfigInterface $config
	 * @param ClassLoader $classLoader
	 * @param array $loadedModules
	 */
	public function __construct(
		ClassLoader $classLoader,
		array $loadedModules=array()
	){		
		$this->setClassLoader($classLoader);
		$this->setConfig($classLoader->getConfig('BricksAsset'));
		$this->setLoadedModeuls($loadedModules);						
	}	
	
	/**
	 * @param ConfigInterface $config
	 */
	public function setConfig(ConfigInterface $config){
		$this->config = $config;
	}
	
	/**
	 * @return \Bricks\Config\ConfigInterface
	 */
	public function getConfig(){
		return $this->config;
	}
	
	/**
	 * @param ClassLoader $classLoader
	 */
	public function setClassLoader(ClassLoader $classLoader){
		$this->classLoader = $classLoader;
	}
	
	/**
	 * @return \Bricks\ClassLoader\ClassLoader
	 */
	public function getClassLoader(){
		return $this->classLoader;
	}
	
	/**
	 * @param array $loadedModules
	 */
	public function setLoadedModules(array $loadedModules=array()){
		$this->loadedModules = $loadedModules;
	}
	
	/**
	 * @return array
	 */
	public function getLoadedModules(){
		return $this->loadedModules;
	}
	
	/**
	 * @param string $moduleName
	 */
	public function hasAsset($moduleName){
		return isset($this->assets[$moduleName]);
	}
	
	/**
	 * @param string $moduleName
	 * @param Module $module
	 */
	public function setAsset($moduleName,Module $module){		
		$this->assets[$moduleName] = $module;
	}
	
	/**
	 * @param string $moduleName
	 * @return Module
	 */
	public function getAsset($moduleName){
		if(!$this->hasAsset($moduleName)){
			$class = $this->getClassLoader()->aliasToClass('assetModule',$moduleName);
			$module = $this->getClassLoader()->get(
				$class,$moduleName,array(
					'Asset' => $this,
					'moduleName' => $moduleName				
				)
			);						
			$this->setAsset($moduleName,$module);
		}
		return $this->assets[$moduleName];
	}
	
	/**
	 * @param array $modules
	 */
	public function autoPublish(array $modules=array()){
		if(0==count($modules)){
			$modules = $this->getLoadedModules();
		}		
		foreach($modules AS $moduleName){
			$module = $this->getAsset($moduleName);
			if($this->getConfig()->get('autoPublish',$moduleName)){								
				$module->publish();
			}
		}
	}
	
	/**
	 * @param array $modules
	 */
	public function autoOptimize(array $modules=array()){
		if(0==count($modules)){
			$modules = $this->getLoadedModules();
		}
		foreach($modules AS $moduleName){
			$module = $this->getAsset($moduleName);
			if($this->getConfig()->get('autoOptimize',$moduleName)){
				$module->optimize();
			}
		}
	}
	
}
