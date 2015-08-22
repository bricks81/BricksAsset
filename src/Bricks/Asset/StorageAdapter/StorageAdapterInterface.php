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

namespace Bricks\Asset\StorageAdapter;

interface StorageAdapterInterface {
	
	/**
	 * @param string $path
	 * @return array
	 */
	public function getSourceDirList($dir);
	
	/**
	 * @param string $source
	 * @return boolean
	 */
	public function isSourceDir($dir);
	
	/**
	 * @param string $file
	 * @return boolean
	 */
	public function isSourceFile($file);
	
	/**
	 * @param string $dir
	 * @return array
	 */
	public function getTargetDirList($dir);
	
	/**
	 * @param string $dir
	 */
	public function removeTargetDir($dir);
	
	/**
	 * @param string $dir
	 * @param numeric $mode
	 * @return \Bricks\File\Directory
	 */
	public function makeTargetDir($dir,$mode=0750);
	
	/**
	 * @param string $dir
	 * @return boolean
	 */
	public function isTargetDir($dir);
	
	/**
	 * @param string $file
	 * @return boolean
	 */
	public function isTargetFile($file);
	
	/**
	 * @param string $file	 
	 */
	public function unlinkTargetFile($file);
	
	/**
	 * @param string $file
	 * @return \Bricks\File\File
	 */
	public function touchTargetFile($file,$mode=0644,$dmode=0750);
	
	/**
	 * @param string $source
	 * @param string $target
	 * @return boolean
	 */
	public function isOlderThan($source,$target);
	
	/**
	 * @param string $source
	 * @param string $target
	 */
	public function copy($source,$target);
	
	/**
	 * @param string $source
	 * @return string
	 */
	public function readSourceFile($source);
	
	/**
	 * @param string $target
	 * @param string $content
	 */
	public function writeTargetFile($target,$content);
	
}