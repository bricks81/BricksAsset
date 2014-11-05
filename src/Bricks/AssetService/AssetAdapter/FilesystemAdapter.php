<?php

namespace Bricks\AssetService\AssetAdapter;

use Bricks\AssetService\AssetModule;
use Bricks\File\Directory;
use Bricks\File\File;

/**
 * Stores module assets using the filesystem
 */
class FilesystemAdapter extends AssetAdapterProvider implements AssetAdapterInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AssetAdapterInterface::getHttpAssetsPath()
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
	 * @see \Bricks\AssetService\Adapter\AssetAdapterInterface::removeHttpAssetsPath()
	 */
	public function removeHttpAssetsPath(AssetModule $module){
		$http_assets_path = $this->getHttpAssetsPath($module);
		if(false==$http_assets_path){
			return;
		}
		Directory::rmdir($http_assets_path);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AssetAdapterInterface::getModuleAssetsPath()
	 */
	public function getModuleAssetsPath(AssetModule $module){
		$module_assets_path = $this->getModuleAssetsPath($module);
		if(is_dir($module_assets_path)){
			return $module_assets_path;
		}
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AssetAdapterInterface::getDirList()
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
	 * @see \Bricks\AssetService\Adapter\AssetAdapterInterface::isSourceDir()
	 */
	public function isSourceDir($source){
		return is_dir($source);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AssetAdapterInterface::isSourceFile()
	 */
	public function isSourceFile($file){
		return is_file($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AssetAdapterInterface::isTargetFile()
	 */
	public function isTargetFile($file){
		return is_file($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AssetAdapterInterface::isOlderThan()
	 */
	public function isOlderThan($source,$target){
		return filectime($source)>filectime($target);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AssetAdapterInterface::copy()
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
	 * @see \Bricks\AssetService\Adapter\AssetAdapterInterface::readSourceFile()
	 */
	public function readSourceFile($source){
		return file_get_contents($source);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AssetAdapterInterface::writeTargetFile()
	 */
	public function writeTargetFile($target,$content){
		if(!is_dir(dirname($target))){
			Directory::mkdir(dirname($target));
		}
		file_put_contents($target,$content);
	}
	
/////////////	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::rmdir()
	 */
	public function rmdir($path,$recursive=true){
		if($recursive){
			Directory::rmdir($path);
		} else {
			rmdir($path);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::copy()
	 */
	public function copy($source,$target,$recursive=true){
		if($recursive){
			File::staticCopy($source,$target);
		} else {
			copy($source,$target);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::touch()
	 */
	public function touch($file,$recursive=true){
		if($recursive){
			File::touch($file);
		} else {
			touch($file);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::read()
	 */
	public function read($file){
		return file_get_contents($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::write()
	 */
	public function write($file,$content){
		file_put_contents($file,$content);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::file_exists()
	 */
	public function fileExists($file){
		return file_exists($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::is_dir($path)
	 */
	public function isDir($path){
		return is_dir($path);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::is_file()
	 */
	public function isFile($file){
		return is_file($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::scandir()
	 */
	public function scandir($dir){
		return scandir($dir);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::filectime()
	 */
	public function filectime($file){
		return filectime($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::realpath()
	 */
	public function realpath($file){
		return realpath($file);
	}
	
}