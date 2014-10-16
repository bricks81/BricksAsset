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
			$update = $this->getLessUpdate($source,$target);
			foreach($update AS $source => $target){
				$this->less($source,$target);
			}
		} else {
			$content = file_get_contents($source);
			$lessc = new lessc();
			$content = $lessc->compile($content);
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
	protected function getLessUpdate($source,$target){
		$update = array();		
		$filelist = scandir($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;						
			if(is_dir($_source)){
				$update += $this->getLessUpdate($_source,$_target);
			} elseif(is_file($_source)){
				if(substr($_source,-5)!='.less'||substr($_source,-9)=='.less.css'){
					continue;
				}
				$_target .= '.css';
				if(!file_exists($_target)||filectime($_source)>filectime($_target)){
					$update[$_source] = $_target;
				}
			}
		}
		return $update;
	}
	
	
}