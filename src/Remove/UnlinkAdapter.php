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

namespace Bricks\Asset\Remove;

use Bricks\Asset\Storage\StorageInterface;

class UnlinkAdapter implements RemoveInterface {
	
	protected $storageAdapter;
	
	public function __construct(StorageInterface $storageAdapter){
		$this->setStorageAdapter($storageAdapter);
	}
	
	public function setStorageAdapter(StorageInterface $storageAdapter){
		$this->storageAdapter = $storageAdapter;
	}
	
	public function getStorageAdapter(){
		return $this->storageAdapter;
	}
	
	public function remove($source,$exclude=array()){
		$count = count($exclude);
		$storage = $this->getStorageAdapter();
		if($storage->is_file($source)){
			if($count && preg_match($exclude,$source)){
				return;
			}
			return $this->removeFile($source);
		} 
		$dh = $storage->opendir($source);
		if($dh){			
			while(false != ($filename = $storage->readdir($dh))){
				if('.'==$filename[0]){
					continue;
				}					
				$path = $source.DIRECTORY_SEPARATOR.$filename;
				if($count && preg_match($exclude,$path)){
					continue;
				}
				if($storage->is_dir($path)){
					$this->remove($path,$exclude);					
				} elseif($storage->is_file($path)){
					$this->removeFile($path);								
				}
				if($storage->is_dir($path) && !count($storage->glob($path.'/*'))){
					$storage->rmdir($path);
				}
			}
			$storage->closedir($dh);
		}
	}
	
	protected function removeFile($source){
		$storage = $this->getStorageAdapter();
		$storage->unlink($source);
	}
	
}