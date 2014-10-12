<?php

namespace Bricks\AssetService\MinifyAdapter;

use \CSSmin;
use \JSMin;
use Bricks\File\File;
use \RuntimeException;

class MrclayMinifyAdapter implements MinifyAdapterInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\MinifyAdapter\MinifyAdapterInterface::minifyCss()
	 */
	public function minifyCss($source,$target){
		if(is_dir($source)){
			$dh = opendir($source);
			if($dh){
				while(false!==($filename=readdir($dh))){
					if('.'==$filename[0]){
						continue;
					}
					$this->minifyCss($source.'/'.$filename,$target.'/'.$filename);
				}
			}
			closedir($dh);
		} else {
			if(substr($source,-4)!=='.css'||substr($source,-8)=='.min.css'){
				return;
			}
			$_target = substr($target,0,strlen($target)-4).'.min.css';
			if(!file_exists($_target)||filectime($source)>filectime($_target)){
				$content = file_get_contents($source);
				$min = new CSSmin();
				$content = $min->run($content);
				file_put_contents($_target,$content);
			}
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\MinifyAdapter\MinifyAdapterInterface::minifyJs()
	 */
	public function minifyJs($source,$target){
		if(is_dir($source)){
			$dh = opendir($source);
			if($dh){
				while(false!==($filename=readdir($dh))){
					if('.'==$filename[0]){
						continue;
					}
					$this->minifyJs($source.'/'.$filename,$target.'/'.$filename);
				}
			}
			closedir($dh);
		} else {
			if(substr($source,-3)!='.js'||substr($source,-7)=='.min.js'){
				return;
			}
			$_target = substr($target,0,strlen($target)-3).'.min.js';
			if(!file_exists($_target)||filectime($source)>filectime($_target)){
				$content = file_get_contents($source);
				$content = JSMin::minify($content);
				file_put_contents($_target,$content);
			}
		}
		
	}	
	
}