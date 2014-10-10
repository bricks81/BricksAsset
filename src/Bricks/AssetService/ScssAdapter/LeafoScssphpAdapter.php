<?php

namespace Bricks\AssetService\ScssAdapter;

use Leafo\ScssPhp\Compiler;
use \RuntimeException;
use Bricks\File\File;

class LeafoScssphpAdapter implements ScssAdapterInterface {

	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\ScssAdapter\ScssAdapterInterface::scss()
	 */
	public function scss($source,$target){
		if(is_dir($source)){
			$dh = opendir($source);
			if($dh){
				while(false!==($filename=readdir($dh))){
					if('.'==$filename||'..'==$filename){
						continue;
					}
					$this->scss($source.'/'.$filename,$target.'/'.$filename);
				}
			}
			closedir($dh);
		} else {
			if(substr($source,-5)!='.scss'&&substr($source,-5)!='.sass'){
				return;
			}
			if(substr($source,-9)=='.scss.css'||substr($source,-9)=='.sass.css'){
				return;
			}			
			$_target = $target.'.css';			
			if(!file_exists($_target)||filectime($source)>filectime($_target)){
				$content = file_get_contents($source);				
				$scss = new Compiler();
				$content = $scss->compile($content);
				file_put_contents($_target,$content);
			}
		}		
	}
	
}