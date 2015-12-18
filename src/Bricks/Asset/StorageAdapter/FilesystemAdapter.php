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

namespace Bricks\Asset\AssetAdapter;

use Bricks\Asset\StorageAdapter\StorageAdapterInterface;
use Bricks\File\Directory;
use Bricks\File\File;

/**
 * Stores module assets using the filesystem
 */
class FilesystemAdapter implements StorageAdapterInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::getSourceDirList()
	 */
	public function getSourceDirList($dir){		
		$files = array();
		if(!is_dir($dir)){
			return $files;
		}
		foreach(scandir($dir) AS $filename){
			if('.'==$filename||'..'==$filename){
				continue;
			}
			$files[] = $filename;
		}		
		return $files;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::isSourceDir()
	 */
	public function isSourceDir($dir){
		return is_dir($dir);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::isSourceFile()
	 */
	public function isSourceFile($file){
		return is_file($file);
	}
	
	public function getTargetDirList($dir){
		$files = array();
		foreach(scandir($dir) AS $filename){
			if('.'==$filename||'..'==$filename){
				continue;
			}
			$files[] = $filename;
		}
		return $files;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::isTargetDir()
	 */
	public function isTargetDir($dir){
		return is_dir($dir);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::removeTargetDir()
	 */
	public function removeTargetDir($dir){
		if(is_dir($dir)){
			Directory::rmdir($dir);
		}		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::makeTargetDir()
	 */
	public function makeTargetDir($dir,$mode=0750){
		return Directory::mkdir($dir,$mode);		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::isTargetFile()
	 */
	public function isTargetFile($file){
		return is_file($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::unlinkTargetFile()
	 */
	public function unlinkTargetFile($file){
		File::unlink($file);		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::touchTargetFile()
	 */
	public function touchTargetFile($file,$mode=0644,$dmode=0750){
		return File::touch($file,$mode,$dmode);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::isOlderThan()
	 */
	public function isOlderThan($source,$target){		
		return filemtime($source)>filemtime($target);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::copy()
	 */
	public function copy($source,$target){
		if($this->isSourceDir($source)){
			$dir = new Directory($source);
			$dir->copy($target);
		} elseif($this->isSourceFile($source)) {
			$file = new File($source);
			$file->copy($target);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::readSourceFile()
	 */
	public function readSourceFile($source){
		return file_get_contents($source);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\AssetAdapter\AssetAdapterInterface::writeTargetFile()
	 */
	public function writeTargetFile($target,$content){
		if(!is_dir(dirname($target))){
			Directory::mkdir(dirname($target));
		}
		file_put_contents($target,$content);
	}
		
}