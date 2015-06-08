<?php
/**
 * Bricks Framework & Bricks CMS
 *
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Asset\ScssStrategy;

use Leafo\ScssPhp\Compiler;
use Bricks\Asset\Module;
use Bricks\Asset\StorageAdapter\StorageAdapterInterface;
use Bricks\Asset\ScssStrategy\ScssStrategyInterface;

class LeafoScssphpStrategy implements ScssStrategyInterface {

	/**
	 * @var Module
	 */
	protected $module;
	
	/**
	 * @var StorageAdapterInterface
	 */
	protected $storageAdapter;
	
	/**
	 * @var Compiler
	 */
	protected $scss;
	
	/**
	 * @param Module $module
	 */
	public function __construct(Module $module){
		$this->setModule($module);
	}
	
	/**
	 * @param Module $module
	 */
	public function setModule(Module $module){
		$this->module = $module;
	}
	
	/**
	 * @return \Bricks\Asset\Module
	 */
	public function getModule(){
		return $this->module;
	}
	
	/**
	 * @param StorageAdapterInterface $adapter
	 */
	public function setStorageAdapter(StorageAdapterInterface $adapter){
		$this->storageAdapter = $adapter;
	}
	
	/**
	 * @return \Bricks\Asset\StorageAdapter\StorageAdapterInterface
	 */
	public function getStorageAdapter(){
		return $this->storageAdapter;
	}
	
	/**
	 * @param Compiler $scss
	 */
	public function setScss(Compiler $scss){
		$this->scss = $scss;
	}
	
	/**
	 * @return \Leafo\ScssPhp\Compiler
	 */
	public function getScss(){
		if(!$this->scss){
			$this->scss = new Compiler();
		}
		return $this->scss;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\ScssStrategy\ScssStrategyInterface::scss()
	 */
	public function scss(){
		$adapter = $this->getStorageAdapter();
		$module = $this->getModule();
		$moduleName = $module->getModuleName();
		$asset = $this->getAsset();
		$config = $asset->getConfig()->getArray($moduleName);
		
		$path = realpath(
			$config['wwwRootPath'].'/'.
			$config['httpAssetsPath'].'/'.
			$moduleName
		);
		if(false==$path){
			return;
		}
		
		$scss = $this->getScss();
		
		$imports = $this->getScssImports($adapter,$path);
		$update = $this->getScssUpdate($adapter,$path,$path);
		
		foreach($imports AS $s => $files){
			foreach($files AS $f){
				$scss->addImportPath(dirname($f));
				if(isset($update[$f])){
					unset($update[$f]);
				}
			}
		}
		
		foreach($update AS $source => $target){
			$content = $adapter->readSourceFile($source);
			$content = $scss->compile($content);
			$adapter->writeTargetFile($target,$content);
		}
	}
	
	/**
	 * @param StorageAdapterInterface $adapter
	 * @param string $source
	 * @return array
	 */
	protected function getScssImports(StorageAdapterInterface $adapter,$source){
		$imports = array();
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]||'_'==$filename[0]){
				continue;
			}
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$imports += $this->getScssImports($adapter,$_source);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-5)!='.scss'&&substr($_source,-5)!='.sass'){
					continue;
				}
				if(substr($_source,-9)=='.scss.css'||substr($_source,-9)=='.sass.css'){
					continue;
				}
				$content = $adapter->readSourceFile($_source);
				if(preg_match_all('#@import(.*?)\n#i',$content,$matches)){					
					foreach($matches[1] AS $string){
						$imports[$_source][] = dirname($_source).'/_'.trim($string,' ";\'').'.scss';
						$imports[$_source][] = dirname($_source).'/_'.trim($string,' ";\'').'.sass';
					}
				}
			}
		}
		return $imports;
	}
	
	/**
	 * @param StorageAdapterInterface
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getScssUpdate(StorageAdapterInterface $adapter,$source,$target){
		$update = array();
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]||'_'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$update += $this->getScssUpdate($adapter,$_source,$_target);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-5)!='.scss'&&substr($_source,-5)!='.sass'){
					continue;
				}
				if(substr($_source,-9)=='.scss.css'||substr($_source,-9)=='.sass.css'){
					continue;
				}
				$_target = $_target.'.css';
				//if(!$adapter->isTargetFile($_target)||$adapter->isOlderThan($_source,$_target)){
					$update[$_source] = $_target;
				//}
			}
		}
		return $update;
	}
	
}