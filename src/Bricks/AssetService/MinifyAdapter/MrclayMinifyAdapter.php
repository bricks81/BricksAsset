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
			$update = $this->getMinifyCssUpdate($source,$target);
			foreach($update AS $source => $target){
				$this->minifyCss($source,$target);
			}
		} else {
			$content = file_get_contents($source);
			$min = new CSSmin();
			$content = $min->run($content);
			if(!file_exists($target)){
				File::touch($target);
			}
			file_put_contents($target,$content);			
		}
	}
	
	/**
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getMinifyCssUpdate($source,$target){
		$update = array();
		$filelist = scandir($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;
			if(is_dir($_source)){
				$update += $this->getMinifyCssUpdate($_source,$_target);
			} elseif(is_file($_source)){
				if(substr($_source,-4)!=='.css'||substr($_source,-8)=='.min.css'){
					continue;
				}
				$_target = substr($_target,0,strlen($_target)-4).'.min.css';
				if(!file_exists($_target)||filectime($_source)>filectime($_target)){
					$update[$_source] = $_target;
				}
			}
		}
		return $update;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\AssetService\MinifyAdapter\MinifyAdapterInterface::minifyJs()
	 */
	public function minifyJs($source,$target){
		if(is_dir($source)){
			$update = $this->getMinifyJsUpdate($source,$target);
			foreach($update AS $source => $target){
				$this->minifyJs($source,$target);
			}
		} else {
			$content = file_get_contents($source);
			$content = JSMin::minify($content);
			if(!file_exists($target)){
				File::touch($target);
			}
			file_put_contents($target,$content);			
		}		
	}	
	
	/**
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getMinifyJsUpdate($source,$target){
		$update = array();
		$filelist = scandir($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;
			if(is_dir($_source)){
				$update += $this->getMinifyJsUpdate($_source,$_target);
			} elseif(is_file($_source)){
				if(substr($_source,-3)!='.js'||substr($_source,-7)=='.min.js'){
					continue;
				}
				$_target = substr($_target,0,strlen($_target)-3).'.min.js';
				if(!file_exists($_target)||filectime($_source)>filectime($_target)){
					$update[$_source] = $_target;
				}
			}
		}
		return $update;
	}
	
}