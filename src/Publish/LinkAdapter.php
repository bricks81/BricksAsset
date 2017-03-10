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

namespace Bricks\Asset\Publish;

use Bricks\Asset\Storage\StorageInterface;

class LinkAdapter implements PublishInterface {
	
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
	
	public function publish($source,$target,$overwrite=false,$exclude=array()){
		$count = count($exclude);
		$storage = $this->getStorageAdapter();
		if($storage->is_file($source)){
			if($count && preg_match($exclude,$source)){
				return;
			}
			return $this->publishFile($source,$target,$overwrite);			
		}
		$dh = $storage->opendir($source);
		if($dh){
			while(false !== ($filename = $storage->readdir($dh))){
				if('.' == $filename[0]){
					continue;
				}				
				$sourcePath = $source.DIRECTORY_SEPARATOR.$filename;
				foreach($exclude AS $regexp){
					if(preg_match($regexp,$sourcePath)){
						continue;
					}
				}
				$targetPath = $target.DIRECTORY_SEPARATOR.$filename;
				if($storage->is_dir($sourcePath)){
					$this->publish($sourcePath,$targetPath,$overwrite,$exclude);
				} elseif(!$storage->is_link($sourcePath) && $storage->is_file($sourcePath)){
					$this->publishFile($sourcePath,$targetPath,$overwrite);
				}
			}
			$storage->closedir($dh);
		}		
	}
	
	protected function publishFile($source,$target,$overwrite=false){
		$storage = $this->getStorageAdapter();
		if($storage->is_file($target) && $overwrite){
			$storage->unlink($target);
		}
		if(!$storage->is_dir(dirname($target))){
			$storage->mkdir(dirname($target));
		}
		if(!$storage->is_file($target)){			
			$storage->symlink($source,$target);
		}		
	}
}