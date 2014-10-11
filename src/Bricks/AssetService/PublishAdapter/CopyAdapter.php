<?php

namespace Bricks\AssetService\PublishAdapter;

use Bricks\File\Directory;
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
			$dh = opendir($source);
			if($dh){
				while(false!==($filename=readdir($dh))){
					if('.'==$filename||'..'==$filename){
						continue;
					}
					$this->publish($source.'/'.$filename,$target.'/'.$filename);										
				}
			}
			closedir($dh);
		} else {
			if(!file_exists($target)||filectime($source)>filectime($target)){				
				File::staticCopy($source,$target);	
			}
		}
	}
	
}