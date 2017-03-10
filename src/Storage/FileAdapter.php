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

namespace Bricks\Asset\Storage;

use Bricks\File\File;
use Bricks\File\Directory;

class FileAdapter implements StorageInterface {
	
	public function is_file($pathname){
		return File::is_file($pathname);
	}
	
	public function is_dir($pathname){
		return File::is_dir($pathname);
	}
	
	public function is_link($pathname){
		return File::is_link($pathname);
	}
	
	public function unlink($pathname){
		return File::unlink($pathname);
	}
	
	public function copy($source,$target){
		$file = new File($source);
		$file->copy($target);
	}
	
	public function opendir($pathname){
		return opendir($pathname);
	}
	
	public function readdir($handle){
		return readdir($handle);
	}
	
	public function closedir($handle){
		return closedir($handle);
	}
	
	public function mkdir($pathname){
		return Directory::mkdir($pathname,0750);
	}
	
	public function rmdir($pathname){
		return Directory::rmdir($pathname);
	}
	
	public function symlink($source,$target){
		$file = new File($source);
		$file->symlink($target);
	}
	
	public function glob($string){
		return Directory::glob($string);
	}
	
	public function cleanFilename($moduleName,$replacement='-'){
		return File::cleanFilename($filename,$replacement);
	}
	
}