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
			$update = $this->getScssUpdate($source,$target);
			foreach($update AS $source => $target){
				$this->scss($source,$target);
			}
		} else {
			$content = file_get_contents($source);				
			$scss = new Compiler();
			$content = $scss->compile($content);
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
	protected function getScssUpdate($source,$target){
		$update = array();
		$filelist = scandir($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;
			if(is_dir($_source)){
				$update += $this->getScssUpdate($_source,$_target);
			} elseif(is_file($_source)){
				if(substr($_source,-5)!='.scss'&&substr($_source,-5)!='.sass'){
					continue;
				}
				if(substr($_source,-9)=='.scss.css'||substr($_source,-9)=='.sass.css'){
					continue;
				}
				$_target = $_target.'.css';
				if(!file_exists($_target)||filectime($_source)>filectime($_target)){
					$update[$_source] = $_target;
				}
			}
		}
		return $update;
	}
	
}