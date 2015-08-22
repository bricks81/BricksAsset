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

namespace Bricks\Asset\ScssStrategy;

use Leafo\ScssPhp\Compiler;
use Bricks\Asset\Module;
use Bricks\Asset\StorageAdapter\StorageAdapterInterface;
use Bricks\Asset\ScssStrategy\ScssStrategyInterface;

class LeafoScssphpStrategy implements ScssStrategyInterface {

	/**
	 * @var Module
	 */
	protected $module;
	
	/**
	 * @var Compiler
	 */
	protected $scss;
	
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
	 * @param Compiler $scss
	 */
	public function setScss(Compiler $scss){
		$this->scss = $scss;
	}
	
	/**
	 * @return \Leafo\ScssPhp\Compiler
	 */
	public function getScss(){
		if(!$this->scss){
			$this->scss = new Compiler();
		}
		return $this->scss;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\ScssStrategy\ScssStrategyInterface::scss()
	 */
	public function scss(){
		$module = $this->getModule();
		$adapter = $module->getStorageAdapter();		
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
		
		$scss = $this->getScss();
		
		$imports = $this->getScssImports($adapter,$path);
		$update = $this->getScssUpdate($adapter,$path,$path);
		
		foreach($imports AS $s => $files){
			foreach($files AS $f){
				$scss->addImportPath(dirname($f));
				if(isset($update[$f])){
					unset($update[$f]);
				}
			}
		}
		
		foreach($update AS $source => $target){
			$content = $adapter->readSourceFile($source);
			$content = $scss->compile($content);
			$adapter->writeTargetFile($target,$content);
		}
	}
	
	/**
	 * @param StorageAdapterInterface $adapter
	 * @param string $source
	 * @return array
	 */
	protected function getScssImports(StorageAdapterInterface $adapter,$source){
		$imports = array();
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]||'_'==$filename[0]){
				continue;
			}
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$imports += $this->getScssImports($adapter,$_source);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-5)!='.scss'&&substr($_source,-5)!='.sass'){
					continue;
				}
				if(substr($_source,-9)=='.scss.css'||substr($_source,-9)=='.sass.css'){
					continue;
				}
				$content = $adapter->readSourceFile($_source);
				if(preg_match_all('#@import(.*?)\n#i',$content,$matches)){					
					foreach($matches[1] AS $string){
						$imports[$_source][] = dirname($_source).'/_'.trim($string,' ";\'').'.scss';
						$imports[$_source][] = dirname($_source).'/_'.trim($string,' ";\'').'.sass';
					}
				}
			}
		}
		return $imports;
	}
	
	/**
	 * @param StorageAdapterInterface
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getScssUpdate(StorageAdapterInterface $adapter,$source,$target){
		$update = array();
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]||'_'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$update += $this->getScssUpdate($adapter,$_source,$_target);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-5)!='.scss'&&substr($_source,-5)!='.sass'){
					continue;
				}
				if(substr($_source,-9)=='.scss.css'||substr($_source,-9)=='.sass.css'){
					continue;
				}
				$_target = $_target.'.css';
				//if(!$adapter->isTargetFile($_target)||$adapter->isOlderThan($_source,$_target)){
					$update[$_source] = $_target;
				//}
			}
		}
		return $update;
	}
	
}