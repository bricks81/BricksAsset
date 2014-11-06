<?php

namespace BricksAssetTest\Mock;

use Bricks\AssetService\AssetAdapter\AssetAdapterInterface;
use Bricks\AssetService\AssetModule;
use Zend\Log\Logger;

class Adapter implements AssetAdapterInterface {
	
	protected $log;
	
	public function setLogger(Logger $log){
		$this->log = $log;
	}
	
	public function getLogger(){
		return $this->log;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::getHttpAssetsPath()
	 */
	public function getHttpAssetsPath(AssetModule $module){
		return $module->getHttpAssetsPath();		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::removeHttpAssetsPath()
	 */
	public function removeHttpAssetsPath(AssetModule $module){		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::getModuleAssetsPath()
	 */
	public function getModuleAssetsPath(AssetModule $module){
		return $module->getModuleAssetsPath();		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\AssetAdapter\AssetAdapterInterface::getSourceDirList()
	 */
	public function getSourceDirList($path){
		return array();
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
		return;
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
		return;
	}
	
}