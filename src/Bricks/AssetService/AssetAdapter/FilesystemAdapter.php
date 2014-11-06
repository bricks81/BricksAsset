<?php

namespace Bricks\AssetService\AssetAdapter;

use Bricks\AssetService\AssetAdapter\AssetAdapterInterface;
use Bricks\AssetService\AssetModule;
use Bricks\File\Directory;
use Bricks\File\File;

/**
 * Stores module assets using the filesystem
 */
class FilesystemAdapter implements AssetAdapterInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::getHttpAssetsPath()
	 */
	public function getHttpAssetsPath(AssetModule $module){
		$http_assets_path = $module->getHttpAssetsPath();
		if(is_dir($http_assets_path)){
			return $http_assets_path;
		}
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::removeHttpAssetsPath()
	 */
	public function removeHttpAssetsPath(AssetModule $module){
		$http_assets_path = $module->getHttpAssetsPath();
		if(false==$http_assets_path){
			return;
		}
		Directory::rmdir($http_assets_path);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::getModuleAssetsPath()
	 */
	public function getModuleAssetsPath(AssetModule $module){
		$module_assets_path = $module->getModuleAssetsPath();
		if(is_dir($module_assets_path)){
			return $module_assets_path;
		}
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::getSourceDirList()
	 */
	public function getSourceDirList($path){
		$files = array();
		foreach(scandir($path) AS $filename){
			if('.'==$filename||'..'==$filename){
				continue;
			}
			$files[] = $filename;
		}
		return $files;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::isSourceDir()
	 */
	public function isSourceDir($source){
		return is_dir($source);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::isSourceFile()
	 */
	public function isSourceFile($file){
		return is_file($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::isTargetFile()
	 */
	public function isTargetFile($file){
		return is_file($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::isOlderThan()
	 */
	public function isOlderThan($source,$target){
		return filectime($source)>filectime($target);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::copy()
	 */
	public function copy($source,$target){
		if($this->isDir($source)){
			$dir = new Directory($source);
			$dir->copy($target);
		} elseif($this->isFile($source)) {
			$file = new File($source);
			$file->copy($target);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::readSourceFile()
	 */
	public function readSourceFile($source){
		return file_get_contents($source);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::writeTargetFile()
	 */
	public function writeTargetFile($target,$content){
		if(!is_dir(dirname($target))){
			Directory::mkdir(dirname($target));
		}
		file_put_contents($target,$content);
	}
		
}