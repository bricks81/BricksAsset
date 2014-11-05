<?php

namespace BricksAssetTest\Mock\BricksAsset\Adapter;

use Bricks\AssetService\Adapter\AdapterInterface;
use Zend\Log\Logger;

class AdapterMock implements AdapterInterface {
	
	protected $log;
	
	public function setLogger(Logger $log){
		$this->log = $log;
	}
	
	public function getLogger(){
		return $this->log;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::rmdir()
	 */
	public function rmdir($path,$recursive=true){		
		$this->getLogger()->debug('rmdir:'.$path);				
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::copy()
	 */
	public function copy($source,$target,$recursive=true){
		$this->getLogger()->debug('copy:'.$source.':'.$target);		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::touch()
	 */
	public function touch($file,$recursive=true){
		$this->getLogger()->debug('touch:'.$file);		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::read()
	 */
	public function read($file){
		$this->getLogger()->debug('read:'.$file);
		return '/**/';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::write()
	 */
	public function write($file,$content){	
		$this->getLogger()->debug('write:'.$file);	
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::file_exists()
	 */
	public function fileExists($file){
		$this->getLogger()->debug('fileExists:'.$file);
		return file_exists($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::is_dir($path)
	 */
	public function isDir($path){
		$this->getLogger()->debug('isDir:'.$path);
		return is_dir($path);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::is_file()
	 */
	public function isFile($file){
		$this->getLogger()->debug('isFile:'.$file);
		return is_file($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::scandir()
	 */
	public function scandir($dir){
		$this->getLogger()->debug('scandir:'.$dir);
		return scandir($dir);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::filectime()
	 */
	public function filectime($file){
		$this->getLogger()->debug('filectime:'.$file);
		return filectime($file);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\Adapter\AdapterInterface::realpath()
	 */
	public function realpath($file){
		$this->getLogger()->debug('realpath:'.$file);
		return realpath($file);
	}
	
}