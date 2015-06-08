<?php
/**
 * Bricks Framework & Bricks CMS
 *
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Asset\LessStrategy;

use \lessc;
use Bricks\Asset\LessStrategy\LessStrategyInterface;
use Bricks\Asset\StorageAdapter\StorageAdapterInterface;
use Bricks\Asset\Module;

class OyejorgeLessphpStrategy implements LessStrategyInterface {
	
	/**
	 * @var Module
	 */
	protected $module;
	
	/**
	 * @var \Bricks\Asset\StorageAdapter\StorageAdapterInterface
	 */
	protected $storageAdapter;
	
	/**
	 * @var \lessc
	 */
	protected $less;
	
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
	 * @param lessc $less
	 */
	public function setLess(lessc $less){
		$this->less = $less;
	}
	
	public function getLess(){
		if(!$this->less){
			$this->less = new lessc();
		}
		return $this->less;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\Asset\LessStrategy\LessStrategyInterface::less()
	 */
	public function less(){
		$module = $this->getModule();
		$moduleName = $module->getModuleName();
		$asset = $module->getAsset();
		$classLoader = $asset->getClassLoader();		
		$adaper = $classLoader->newInstance(__CLASS__,__METHOD__,'storageAdapterClass',$moduleName,array(
			'Asset' => $asset,			
		));
		$config = $asset->getConfig()->getArray($moduleName);		
		
		$path = realpath(
			$config['wwwRootPath'].'/'.
			$config['httpAssetsPath'].'/'.
			$moduleName
		);
		if(false==$path){
			return;
		}
		$lessc = $this->getLess();
		
		$imports = $this->getLessImports($adapter,$path);
		$update = $this->getLessUpdate($adapter,$path,$path);
		foreach($imports AS $s => $files){
			foreach($files AS $f){
				$lessc->addImportDir(dirname($f));
				if(isset($update[$f])){
					unset($update[$f]);
				}
			}			
		}				
		
		foreach($update AS $source => $target){
			$content = $adapter->readSourceFile($source);
			$content = $lessc->compile($content);
			$adapter->writeTargetFile($target,$content);
		}
	}
	
	/**
	 * @param StorageAdapterInterface $adapter
	 * @param string $source
	 * @return array
	 */
	protected function getLessImports(StorageAdapterInterface $adapter,$source){
		$imports = array();
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$imports += $this->getLessImports($adapter,$_source);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-5)!='.less'||substr($_source,-9)=='.less.css'){
					continue;
				}
				$content = $adapter->readSourceFile($_source);
				if(preg_match_all('#@import(.*?)\n#i',$content,$matches)){
					foreach($matches[1] AS $string){
						$imports[$_source][] = dirname($_source).'/'.trim($string,' ";\'');
					}					
				}
			}			
		}
		return $imports;
	}
	
	/**
	 * @param AssetAdapterInterface $adapter
	 * @param string $source
	 * @param string $target
	 * @return array
	 */
	protected function getLessUpdate(StorageAdapterInterface $adapter,$source,$target){
		$update = array();		
		$filelist = $adapter->getSourceDirList($source);
		foreach($filelist AS $filename){
			if('.'==$filename[0]){
				continue;
			}
			$_target = $target.'/'.$filename;
			$_source = $source.'/'.$filename;
			if($adapter->isSourceDir($_source)){
				$update += $this->getLessUpdate($adapter,$_source,$_target);
			} elseif($adapter->isSourceFile($_source)){
				if(substr($_source,-5)!='.less'||substr($_source,-9)=='.less.css'){
					continue;
				}
				$_target .= '.css';
				//if(!$adapter->isTargetFile($_target)||$adapter->isOlderThan($_source,$_target)){
					$update[$_source] = $_target;
				//}
			}
		}
		return $update;
	}
	
	
}