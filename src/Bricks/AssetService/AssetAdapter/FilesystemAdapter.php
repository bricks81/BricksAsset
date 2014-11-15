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
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::getSourceDirList()
	 */
	public function getSourceDirList($dir){
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
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::isSourceDir()
	 */
	public function isSourceDir($dir){
		return is_dir($dir);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::isSourceFile()
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
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::isTargetDir()
	 */
	public function isTargetDir($dir){
		return is_dir($dir);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::removeTargetDir()
	 */
	public function removeTargetDir($dir){
		Directory::rmdir($dir);		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::makeTargetDir()
	 */
	public function makeTargetDir($dir,$mode=0750){
		return Directory::mkdir($dir,$mode);		
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
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::unlinkTargetFile()
	 */
	public function unlinkTargetFile($file){
		File::unlink($file);		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::touchTargetFile()
	 */
	public function touchTargetFile($file,$mode=0644,$dmode0750){
		return File::touch($file,$mode,$dmode);
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