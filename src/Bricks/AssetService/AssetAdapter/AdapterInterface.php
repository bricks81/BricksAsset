<?php

namespace Bricks\AssetService\AssetAdapter;

use Bricks\AssetService\AssetModule;

interface AssetAdapterInterface {
	
	/**
	 * Gets the assets path of the given module
	 * If it not exists, it will return false
	 * 
	 * @param AssetModule $module
	 * @return string|false
	 */
	public function getHttpAssetsPath(AssetModule $module);
	
	/**
	 * Removes the http assets path
	 * 
	 * @param AssetModule $module
	 */
	public function removeHttpAssetsPath(AssetModule $module);
	
	/**
	 * @param AssetModule $module
	 * 
	 * @return string|false
	 */
	public function getModuleAssetsPath(AssetModule $module);
	
	/**
	 * @param string $path
	 * @return array
	 */
	public function getSourceDirList($path);
	
	/**
	 * @param string $source
	 * @return boolean
	 */
	public function isSourceDir($source);
	
	/**
	 * @param string $file
	 * @return boolean
	 */
	public function isSourceFile($file);
	
	/**
	 * @param string $file
	 * @return boolean
	 */
	public function isTargetFile($file);
	
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