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

use Bricks\Asset\Asset;
use Zend\EventManager\EventInterface;
use Bricks\Asset\StorageAdapter\StorageAdapterInterface;
use Bricks\Asset\PublishStrategy\PublishStrategyInterface;
use Bricks\Asset\LessStrategy\LessStrategyInterface;
use Bricks\Asset\ScssStrategy\ScssStrategyInterface;
use Bricks\Asset\MinifyCssStrategy\MinifyCssStrategyInterface;
use Bricks\Asset\MinifyJsStrategy\MinifyJsStrategyInterface;
use Bricks\Asset\RemoveStrategy\RemoveStrategyInterface;

/**
 * Represents a module
 * 
 * The parameter $isDefault on the setter methods will be used
 * when the defaults on the asset service will be changed.
 * If it's true each change on the asset service
 * will change this specific value in the module, too.
 */
class Module {
	
	/**
	 * @var \Bricks\Asset\Asset
	 */
	protected $asset;
	
	/**
	 * @var string
	 */
	protected $moduleName;
	
	/**
	 * @var string
	 */
	protected $namespace;
	
	/**
	 * @var array
	 */
	protected $storageAdapters = array();
	
	/**
	 * @var array
	 */
	protected $publishStrategies = array();
	
	/**
	 * @var array
	 */
	protected $lessStrategies = array();
	
	/**
	 * @var array
	 */
	protected $scssStrategies;
	
	/**
	 * @var array
	 */
	protected $minifyCssStrategies;
	
	/**
	 * @var array
	 */
	protected $minifyJsStrategies;
	
	/**
	 * @var array
	 */
	protected $removeStrategies;
	
	/**	
	 * @param Asset $asset
	 * @param string $moduleName
	 */
	public function __construct(Asset $asset,$moduleName,$defaultNamespace=null){
		$this->setAsset($asset);
		$this->setModuleName($moduleName);		
		$this->setNamespace($defaultNamespace?:$moduleName);		
	}
	
	/**
	 * @param Asset $asset
	 */
	public function setAsset(Asset $asset){
		$this->asset = $asset;		
	}
	
	/**
	 * @return \Bricks\Asset\Asset
	 */
	public function getAsset(){
		return $this->asset;
	}
	
	/**
	 * @param string $moduleName
	 */
	public function setModuleName($moduleName){
		$this->moduleName = $moduleName;
	}
	
	/**
	 * @return string
	 */
	public function getModuleName(){
		return $this->moduleName;
	}
	
	/**
	 * @param string $namespace
	 */
	public function setNamespace($namespace=null){
		$this->namespace = $namespace;
	}
	
	/**
	 * @return string
	 */
	public function getNamespace(){
		return $this->namespace;
	}
	
	/**
	 * @param string $namespace
	 * @return StorageAdapterInterface
	 */
	public function getStorageAdapter($namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		if(!isset($this->storageAdapters[$namespace])){
			$class = $this->getAsset()->getClassLoader()->aliasToClass('BricksAsset.storageAdapter',$namespace);
			$this->storageAdapters[$namespace] = $this->getAsset()->getClassLoader()->get(
				$class,$namespace,array(
					'Module' => $this,
					'namespace' => $namespace
				)
			);
		}
		return $this->storageAdapters[$namespace];
	}
	
	/**
	 * @param StorageAdapterInterface $adapter
	 * @param string $namespace
	 */
	public function setStorageAdapter(StorageAdapterInterface $adapter,$namespace=null){
		$this->storageAdapters[$namespace] = $adapter;
	}
	
	/**
	 * @param string $namespace
	 * @return PublishStrategyInterface
	 */
	public function getPublishStrategy($namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		if(!isset($this->publishStrategies[$namespace])){
			$class = $this->getAsset()->getClassLoader()->aliasToClass('BricksAsset.publishStrategy',$namespace);
			$this->publishStrategies[$namespace] = $this->getAsset()->getClassLoader()->get(
				$class,$namespace,array(
					'Module' => $this,
					'namespace' => $namespace
				)
			);
		}
		return $this->publishStrategies[$namespace];
	}
	
	/**
	 * @param PublishStrategyInterface $strategy
	 * @param string $namespace
	 */
	public function setPublishStrategy(PublishStrategyInterface $strategy,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		$this->publishStrategies[$namespace] = $strategy;
	}
	
	/**
	 * @param string $namespace
	 * @return LessStrategyInterface
	 */
	public function getLessStrategy($namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		if(!isset($this->lessStrategies[$namespace])){
			$class = $this->getAsset()->getClassLoader()->aliasToClass('BricksAsset.lessStrategy',$namespace);
			$this->lessStrategies[$namespace] = $this->getAsset()->getClassLoader()->get(
				$class,$namespace,array(
					'Module' => $this,
					'namespace' => $namespace
				)
			);
		}
		return $this->lessStrategies[$namespace];
	}
	
	/**
	 * @param LessStrategyInterface $strategy
	 * @param string $namespace
	 */
	public function setLessStrategy(LessStrategyInterface $strategy,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		$this->lessStrategies[$namespace] = $strategy;
	}
	
	/**
	 * @param string $namespace
	 * @return ScssStrategyInterface
	 */
	public function getScssStrategy($namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		if(!isset($this->scssStrategies[$namespace])){
			$class = $this->getAsset()->getClassLoader()->aliasToClass('BricksAsset.scssStrategy',$namespace);
			$this->lessStrategies[$namespace] = $this->getAsset()->getClassLoader()->get(
				$class,$namespace,array(
					'Module' => $this,
					'namespace' => $namespace
				)
			);
		}
		return $this->scssStrategies[$namespace];
	}
	
	/**
	 * @param ScssStrategyInterface $strategy
	 * @param string $namespace
	 */
	public function setScssStrategy(ScssStrategyInterface $strategy,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		$this->scssStrategies[$namespace] = $strategy;
	}
	
	/**
	 * @param string $namespace
	 * @return MinifyCssStrategyInterface
	 */
	public function getMinifyCssStrategy($namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		if(!isset($this->minifyCssStrategies[$namespace])){
			$class = $this->getAsset()->getClassLoader()->aliasToClass('BricksAsset.minifyCssStrategy',$namespace);
			$this->lessStrategies[$namespace] = $this->getAsset()->getClassLoader()->get(
				$class,$namespace,array(
					'Module' => $this,
					'namespace' => $namespace
				)
			);
		}
		return $this->minifyCssStrategies[$namespace];
	}
	
	/**
	 * @param MinifyCssStrategyInterface $strategy
	 * @param string $namespace
	 */
	public function setMinifyCssStrategy(MinifyCssStrategyInterface $strategy,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		$this->minifyCssStrategies[$namespace] = $strategy;
	}
	
	/**
	 * @param string $namespace
	 * @return MinifyJsStrategyInterface
	 */
	public function getMinifyJsStrategy($namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		if(!isset($this->minifyJsStrategies[$namespace])){
			$class = $this->getAsset()->getClassLoader()->aliasToClass('BricksAsset.minifyJsStrategy',$namespace);
			$this->lessStrategies[$namespace] = $this->getAsset()->getClassLoader()->get(
				$class,$namespace,array(
					'Module' => $this,
					'namespace' => $namespace
				)
			);
		}
		return $this->minifyJsStrategies[$namespace];
	}
	
	/**
	 * @param MinifyJsStrategyInterface $strategy
	 * @param string $namespace
	 */
	public function setMinifyJsStrategy(MinifyJsStrategyInterface $strategy,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		$this->minifyJsStrategies[$namespace] = $strategy;
	}
	
	/**
	 * @param string $namespace
	 * @return RemoveStrategyInterface
	 */
	public function getRemoveStrategy($namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		if(!isset($this->removeStrategies[$namespace])){
			$class = $this->getAsset()->getClassLoader()->aliasToClass('BricksAsset.removeStrategy',$namespace);
			$this->lessStrategies[$namespace] = $this->getAsset()->getClassLoader()->get(
				$class,$namespace,array(
					'Module' => $this,
					'namespace' => $namespace
				)
			);
		}
		return $this->removeStrategies[$namespace];
	}
	
	/**
	 * @param RemoveStrategyInterface $strategy
	 * @param string $namespace
	 */
	public function setRemoveStrategy(RemoveStrategyInterface $strategy,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		$this->removeStrategies[$namespace] = $strategy;
	}
	
	public function publish(){
		$publish = $this->getPublishStrategy();
		$publish->publish();	
		if($this->getAsset()->getConfig()->get('lessSupport',$this->getModuleName())){
			$strategy = $this->getLessStrategy();
			$strategy->less();			
		}
		if($this->getAsset()->getConfig()->get('scssSupport',$this->getModuleName())){
			$strategy = $this->getScssStrategy();
			$strategy->scss();
		}
	}
	
	public function optimize(){
		if($this->getAsset()->getConfig()->get('minifyCssSupport',$this->getModuleName())){
			$strategy = $this->getMinifyCssStrategy();
			$strategy->minify();			
		}
		if($this->getAsset()->getConfig()->get('minifyJsSupport',$this->getModuleName())){
			$strategy = $this->getMinifyJsStrategy();
			$strategy->minify();			
		}		
	}
	
	public function remove(){
		$remove = $this->getRemoveStrategy();
		$remove->remove();
	}
	
}