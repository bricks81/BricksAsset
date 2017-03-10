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

use Bricks\Config\ConfigAwareInterface;
use Bricks\Config\ConfigInterface;
use Bricks\Loader\LoaderAwareInterface;
use Bricks\Loader\LoaderInterface;

class Asset implements ConfigAwareInterface, LoaderAwareInterface {
	
	const ADAPTER_PUBLISH 	= 'adapterPublish';
	const ADAPTER_REMOVE 	= 'adapterRemove';
	const ADAPTER_OPTIMIZE 	= 'adapterOptimize';
	const ADAPTER_STORAGE	= 'adapterStorage';
	const ADAPTER_COMPILE	= 'adapterCompile';

    /**
     * @var ConfigInterface
     */
	protected $config;

    /**
     * @var LoaderInterface
     */
	protected $loader;

    /**
     * @var array
     */
	protected $adapters = array();

    /**
     * @var array
     */
	protected $allowedPathes = array();
	
	public function setConfig(ConfigInterface $config){
	    $this->config = $config;
    }
	
	public function getConfig(){
		return $this->config;
	}

	public function setLoader(LoaderInterface $loader){
	    $this->loader = $loader;
    }
	
	public function getLoader(){
		return $this->loader;
	}
	
	public function getAdapter($type,$args=array(),$namespace=null){
		$namespace = $namespace?:$this->getConfig()->getDefaultNamespace();
		if(!isset($this->adapters[$namespace][$type])){
			switch($type){
				case self::ADAPTER_PUBLISH:				
					$class = $this->getConfig()->get('bricks-asset;publishAdapter',$namespace);
					$this->adapters[$namespace][$type] = 
						$this->getLoader()->get($class,$args,$namespace);					
					break;
				case self::ADAPTER_REMOVE:
					$class = $this->getConfig()->get('bricks-asset;removeAdapter',$namespace);
					$this->adapters[$namespace][$type] = 
						$this->getLoader()->get($class,$args,$namespace);					
					break;
				case self::ADAPTER_OPTIMIZE:
					$class = $this->getConfig()->get('bricks-asset;optimizeAdapter',$namespace);
					$this->adapters[$namespace][$type] = 
						$this->getLoader()->get($class,$args,$namespace);					
					break;
				case self::ADAPTER_STORAGE:
					$class = $this->getConfig()->get('bricks-asset;storageAdapter',$namespace);
						$this->adapters[$namespace][$type] =
						$this->getLoader()->get($class,$args,$namespace);
					break;
				case self::ADAPTER_COMPILE:
					$class = $this->getConfig()->get('bricks-asset;compileAdapter',$namespace);
						$this->adapters[$namespace][$type] =
						$this->getLoader()->get($class,$args,$namespace);
					break;
			}
		}
		if(isset($this->adapters[$namespace][$type])){
			return $this->adapters[$namespace][$type];
		}
	}
	
	public function setAdapter($type,$namespace,$adapter){
		$this->adapters[$namespace][$type] = $adapter;
	}
	
	public function getAssetsDir($moduleName,$ignorePathRestriction=false){
		$assetsDir = str_replace('/',DIRECTORY_SEPARATOR,$this->getConfig()->get('BricksAsset.publicAssetsDir',$moduleName));
		if(!$assetsDir){
			throw new Exception\NoAssetPath('there is no public asset path configured');
		}
		if(!$path = realpath($assetsDir)){
			throw new Exception\AssetPathNotExists('the given asset dir not exists: "'.$assetsDir. '"');
		}
		if(substr($path,0,strlen(getcwd())) != getcwd() && !$ignorePathRestriction){
			throw new Exception\PathRestriction('the given assets dir is not in current working directory: "'.$assetsDir."'");
		}
		return $assetsDir;
	}
	
	public function publish($moduleName,$overwrite=false,$exclude=array()){		
		if(	$source = str_replace('/',DIRECTORY_SEPARATOR,$this->getConfig()->get('bricks-asset;assets.'.$moduleName,$moduleName))){
			$storageAdapter = $this->getAdapter(self::ADAPTER_STORAGE,array(),$moduleName);
			$publishAdapter = $this->getAdapter(self::ADAPTER_PUBLISH,array($storageAdapter),$moduleName);
			$assetsDir = $this->getAssetsDir($moduleName);
			$target = $assetsDir.DIRECTORY_SEPARATOR.$storageAdapter->cleanFilename($moduleName);
			$exclude = array_merge(
				$exclude,
				($this->getConfig()->get('bricks-asset;assets;'.$moduleName.';exclude',$moduleName)?:array())
			);			
			$publishAdapter->publish($source,$target,$overwrite,$exclude);
		}
	}
	
	public function remove($moduleName,$exclude=array()){
		$storageAdapter = $this->getAdapter(self::ADAPTER_STORAGE,array(),$moduleName);
		$removeAdapter = $this->getAdapter(self::ADAPTER_REMOVE,array($storageAdapter),$moduleName);
		$assetsDir = $this->getAssetsDir($moduleName);
		$target = $assetsDir.DIRECTORY_SEPARATOR.$storageAdapter->cleanFilename($moduleName);
		$exclude = array_merge(
			$exclude,
			($this->getConfig()->get('bricks-asset;assets;'.$moduleName.';exclude',$moduleName)?:array())
			
		);
		$removeAdapter->remove($target,$exclude);		
	}
	
	public function compile($moduleName,$overwrite=false,$exclude=array()){
		$storageAdapter = $this->getAdapter(self::ADAPTER_STORAGE,array(),$moduleName);
		$compileAdapter = $this->getAdapter(self::ADAPTER_COMPILE,array($storageAdapter),$moduleName);
		$assetsDir = $this->getAssetsDir($moduleName);
		$target = $assetsDir.DIRECTORY_SEPARATOR.$storageAdapter->cleanFilename($moduleName);
		$exclude = array_merge(
			$exclude,
			($this->getConfig()->get('bricks-asset;assets;'.$moduleName.';exclude',$moduleName)?:array())
		);
		$compileAdapter->compile($target,$overwrite,$exclude);		
	}
	
	public function optimize($moduleName,$overwrite=false,$exclude=array()){
		$storageAdapter = $this->getAdapter(self::ADAPTER_STORAGE,array(),$moduleName);
		$optimizeAdapter = $this->getAdapter(self::ADAPTER_OPTIMIZE,array($storageAdapter),$moduleName);
		$assetsDir = $this->getAssetsDir($moduleName);
		$target = $assetsDir.DIRECTORY_SEPARATOR.$storageAdapter->cleanFilename($moduleName);
		$exclude = array_merge(
			$exclude,
			($this->getConfig()->get('bricks-asset;assets;'.$moduleName.';exclude',$moduleName)?:array())
		);
		$optimizeAdapter->optimize($target,$overwrite,$exclude);
	}
}