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

namespace Bricks\Asset\RemoveStrategy;

use Bricks\Asset\Module;
use Bricks\Asset\RemoveStrategy\RemoveStrategyInterface;
use Bricks\Asset\StorageAdapter\StorageAdapterInterface;

class RemoveStrategy implements RemoveStrategyInterface {
	
	/**
	 * @var Module
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
	 * @param StorageAdapterInterface $adapter
	 */
	public function setStorageAdapter(StorageAdapterInterface $adapter){
		$this->storageAdapter = $adapter;
	}
	
	/**
	 * @return \Bricks\Asset\StorageAdapter\StorageAdapterInterface
	 */
	public function getStorageAdapter(){
		return $this->storageAdapter;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\RemoveStrategy\RemoveStrategyInterface::remove()
	 */
	public function remove($moduleName){
		$adapter = $this->getStorageAdapter();
		$module = $this->getModule();
		$moduleName = $module->getModuleName();
		$asset = $module->getAsset();
		$config = $asset->getConfig()->getArray($moduleName);
		
		$path = realpath(
			$config['wwwRootPath'].'/'.
			$config['httpAssetsPath'].'/'.
			$moduleName
		);
		if(false==$path){
			return;
		}
		
		$files = $this->getRemoveFiles($adapter,$path);		
		$exclude = $config['removeExclude'];		
		$nodir = array();
		if(0<count($exclude)){			
			foreach($files AS $i => $target){
				foreach($exclude AS $expression){
					if(preg_match($expression,$target)){
						unset($files[$i]);
						if(false===array_search(dirname($target),$nodir)){		
							$nodir[] = dirname($target);
						}
					}
				}
			}
		}
		
		$dirs = array();
		foreach($files AS $i => $target){
			$adapter->unlinkTargetFile($target);
			if(false===array_search(dirname($target),$dirs)){
				$dirs[] = dirname($target);
			}
		}
		
		foreach($dirs AS $dir){
			if(false===array_search($dir,$nodir)){
				if($adapter->isTargetDir($dir)){									
					$adapter->removeTargetDir($dir);
				}
			}
		}
	}
	
	/**
	 * @param StorageAdapterInterface $adapter
	 * @param string $path
	 * @return array
	 */
	protected function getRemoveFiles(StorageAdapterInterface $adapter,$path){
		$files = array();
		$filelist = $adapter->getTargetDirList($path);		
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$file = $path.'/'.$filename;
			if($adapter->isTargetDir($file)){
				$files = array_merge($files,$this->getRemoveFiles($adapter,$file));			
			} elseif($adapter->isTargetFile($file)){
				$files[] = $file;
			}
		}
		return $files;
	}
	
}