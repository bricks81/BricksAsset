<?php

namespace Bricks\AssetService\PublishAdapter;

use Bricks\File\File;

/**
 * This adapter copies a file to the public resource directory
 */
class CopyAdapter implements PublishAdapterInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \BricksAsset\AssetService\PublishAdapter\PublishAdapterInterface::publish()
	 */
	public function publish($source,$target){		
		if(is_dir($source)){
			$update = $this->getPublishUpdate($source,$target);
			foreach($update AS $source => $target){
				$this->publish($source,$target);
			}
		} else {
			File::staticCopy($source,$target);
		}
	}
	
	/**
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getPublishUpdate($source,$target){
		$update = array();
		$filelist = scandir($source);				
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;
			if(is_dir($_source)){
				$update += $this->getPublishUpdate($_source,$_target);
			} elseif(is_file($_source)){
				if(!file_exists($_target)||filectime($_source)>filectime($_target)){
					$update[$_source] = $_target;
				}
			}
		}
		return $update;
	}
	
}