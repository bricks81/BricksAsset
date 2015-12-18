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

namespace Bricks\Asset\LessStrategy;

use \lessc;
use Bricks\Asset\LessStrategy\LessStrategyInterface;
use Bricks\Asset\StorageAdapter\StorageAdapterInterface;
use Bricks\Asset\Module;

class OyejorgeLessphpStrategy implements LessStrategyInterface {
	
	/**
	 * @var Module
	 */
	protected $module;
	
	/**
	 * @var \Bricks\Asset\StorageAdapter\StorageAdapterInterface
	 */
	protected $storageAdapter;
	
	/**
	 * @var \lessc
	 */
	protected $less;
	
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
	 * @param lessc $less
	 */
	public function setLess(lessc $less){
		$this->less = $less;
	}
	
	public function getLess(){
		if(!$this->less){
			$this->less = new lessc();
		}
		return $this->less;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\LessStrategy\LessStrategyInterface::less()
	 */
	public function less(){
		$module = $this->getModule();
		$moduleName = $module->getModuleName();
		$asset = $module->getAsset();
		$classLoader = $asset->getClassLoader();		
		$adapter = $classLoader->get('BricksAsset.storageAdapter',$moduleName,array(
			'Asset' => $asset,			
		));
		$config = $asset->getConfig()->getArray($moduleName);		
		
		$path = realpath(
			$config['wwwRootPath'].'/'.
			$config['httpAssetsPath'].'/'.
			$moduleName
		);
		if(false==$path){
			return;
		}
		$lessc = $this->getLess();
		
		$imports = $this->getLessImports($adapter,$path);
		$update = $this->getLessUpdate($adapter,$path,$path);
		foreach($imports AS $s => $files){
			foreach($files AS $f){
				$lessc->addImportDir(dirname($f));
				if(isset($update[$f])){
					unset($update[$f]);
				}
			}			
		}				
		
		foreach($update AS $source => $target){
			$content = $adapter->readSourceFile($source);
			$content = $lessc->compile($content);
			$adapter->writeTargetFile($target,$content);
		}
	}
	
	/**
	 * @param StorageAdapterInterface $adapter
	 * @param string $source
	 * @return array
	 */
	protected function getLessImports(StorageAdapterInterface $adapter,$source){
		$imports = array();
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$imports += $this->getLessImports($adapter,$_source);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-5)!='.less'||substr($_source,-9)=='.less.css'){
					continue;
				}
				$content = $adapter->readSourceFile($_source);
				if(preg_match_all('#@import(.*?)\n#i',$content,$matches)){
					foreach($matches[1] AS $string){
						$imports[$_source][] = dirname($_source).'/'.trim($string,' ";\'');
					}					
				}
			}			
		}
		return $imports;
	}
	
	/**
	 * @param AssetAdapterInterface $adapter
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getLessUpdate(StorageAdapterInterface $adapter,$source,$target){
		$update = array();		
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$update += $this->getLessUpdate($adapter,$_source,$_target);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-5)!='.less'||substr($_source,-9)=='.less.css'){
					continue;
				}
				$_target .= '.css';
				//if(!$adapter->isTargetFile($_target)||$adapter->isOlderThan($_source,$_target)){
					$update[$_source] = $_target;
				//}
			}
		}
		return $update;
	}
	
	
}