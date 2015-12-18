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

namespace Bricks\Asset\PublishStrategy;

use Bricks\Asset\PublishStrategy\PublishStrategyInterface;
use Bricks\Asset\StorageAdapter\StorageAdapterInterface;
use Bricks\Asset\Module;

class CopyStrategy implements PublishStrategyInterface {
	
	/**
	 * @var \Bricks\Asset\Module
	 */
	protected $module;
	
	/**
	 * @var \Bricks\Asset\StorageAdapter\StorageAdapterInterface
	 */
	protected $storageAdapter;
	
	/**
	 * @param Module $module
	 */
	public function __construct(Module $module){
		$this->setModule($module);
	}
	
	/**
	 * @param Module $module
	 */
	public function setModule(Module $module){
		$this->module = $module;
	}
	
	/**
	 * @return \Bricks\Asset\Module
	 */
	public function getModule(){
		return $this->module;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\PublishStrategy\PublishStrategyInterface::publish()
	 */
	public function publish(){		
		$adapter = $this->getModule()->getStorageAdapter();
		$module = $this->getModule();
		$moduleName = $module->getModuleName();
		$asset = $module->getAsset();
		$config = $asset->getConfig()->getArray($moduleName);
		
		$modulePath = $config['moduleAssetsPath'];
		$root = realpath($config['wwwRootPath']);
		$path = $root.'/'.$config['httpAssetsPath'].'/'.$moduleName;
		if(false==$modulePath||false==$root){			
			return;
		}
		
		$update = $this->getPublishUpdate($adapter,$modulePath,$path);
		
		// exclude patterns
		$exclude = $config['publishExclude'];
		if(0<count($exclude)){
			foreach($update AS $source => $target){
				foreach($exclude AS $expression){
					if(preg_match($expression,$source)){
						unset($update[$source]);
					}
				}
			}
		}
		
		foreach($update AS $source => $target){			
			$adapter->copy($source,$target);
		}		
	}
	
	/**
	 * @param StorageAdapterInterface $adapter
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getPublishUpdate(StorageAdapterInterface $adapter,$source,$target){
		$update = array();		
		$filelist = $adapter->getSourceDirList($source);						
		foreach($filelist AS $filename){		
			if('.'==$filename[0]){
				continue;
			}
			$_source = $source.'/'.$filename;
			$_target = $target.'/'.$filename;			
			if($adapter->isSourceDir($_source)){
				$update += $this->getPublishUpdate($adapter,$_source,$_target);
			} elseif($adapter->isSourceFile($_source)){				
				if(!$adapter->isTargetFile($_target)||$adapter->isOlderThan($_source,$_target)){					
					$update[$_source] = $_target;
				}
			}
		}
		return $update;
	}
	
}