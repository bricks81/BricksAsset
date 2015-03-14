<?php

namespace Bricks\Asset\AssetAdapter;

use Bricks\Asset\AssetModule;
use Bricks\File\Directory;
use Bricks\File\File;

interface AssetAdapterInterface {
	
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
	 * @return Directory
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
	 * @return File
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