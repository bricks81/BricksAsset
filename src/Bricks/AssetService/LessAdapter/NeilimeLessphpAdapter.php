<?php

namespace Bricks\AssetService\LessAdapter;

use \lessc;
use \RuntimeException;

use Bricks\File\File;

class NeilimeLessphpAdapter implements LessAdapterInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\LessAdapter\LessAdapterInterface::less()
	 */
	public function less($source,$target){
		if(is_dir($source)){
			$dh = opendir($source);
			if($dh){
				while(false!==($filename=readdir($dh))){
					if('.'==$filename||'..'==$filename){
						continue;
					}
					$this->less($source.'/'.$filename,$target.'/'.$filename);
				}
			}
			closedir($dh);
		} else {
			if(substr($source,-5)!='.less'||substr($source,-9)=='.less.css'){
				return;
			}
			$_target = $target.'.css';
			if(!file_exists($_target)||filectime($source)>filectime($_target)){				
				$content = file_get_contents($source);
				$lessc = new lessc();
				$content = $lessc->compile($content);
				file_put_contents($_target,$content);
			}
		}		
	}
	
}