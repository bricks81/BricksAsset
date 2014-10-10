<?php

namespace BricksAssetTest\Mocking\AssetService\PublishAdapter;

use Zend\Log\Logger;
use Bricks\AssetService\PublishAdapter\PublishAdapterInterface;
/**
 * This adapter copies a file to the public resource directory
 */
class MockAdapter implements PublishAdapterInterface {
	
	static protected $logger = null;
	
	static public function setLogger(Logger $log){
		self::$logger = $log;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \BricksAsset\AssetService\PublishAdapter\PublishAdapterInterface::publish()
	 */
	public function publish($source,$target){
		if(!self::$logger){
			throw new \RuntimeException('please define a logger for the AssetService MockAdapter');
		}
		self::$logger->debug($source);
		self::$logger->debug($target);
	}
	
}