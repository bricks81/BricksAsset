<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *  
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Asset;

use Bricks\Asset\Asset;
use Zend\EventManager\EventInterface;

/**
 * Represents a module
 * 
 * The parameter $isDefault on the setter methods will be used
 * when the defaults on the asset service will be changed.
 * If it's true each change on the asset service
 * will change this specific value in the module, too.
 */
class Module {
	
	/**
	 * @var \Bricks\Asset\Asset
	 */
	protected $asset;
	
	/**
	 * @var string
	 */
	protected $moduleName;
	
	/**
	 * @var \Bricks\Asset\PublishStrategy\PublishStrategyInterface
	 */
	protected $publishStrategy;
	
	/**
	 * @var \Bricks\Asset\LessStrategy\LessStrategyInterface
	 */
	protected $lessStrategy;
	
	/**
	 * @var \Bricks\Asset\ScssStrategy\ScssStrategyInterface
	 */
	protected $scssStrategy;
	
	/**
	 * @var \Bricks\Asset\MinifyCssStrategy\MinifyCssStrategyInterface
	 */
	protected $minifyCssStrategy;
	
	/**
	 * @var \Bricks\Asset\MinifyJsStrategy\MinifyJsStrategyInterface
	 */
	protected $minifyJsStrategy;
	
	/**
	 * @var \Bricks\Asset\RemoveStrategy\RemoveStrategyInterface
	 */
	protected $removeStrategy;
	
	/**	
	 * @param Asset $asset
	 * @param string $moduleName
	 */
	public function __construct(Asset $asset,$moduleName){
		$this->setAsset($asset);
		$this->setModuleName($moduleName);
	}
	
	/**
	 * @param Asset $asset
	 */
	public function setAsset(Asset $asset){
		$this->asset = $asset;		
	}
	
	/**
	 * @return \Bricks\Asset\Asset
	 */
	public function getAsset(){
		return $this->asset;
	}
	
	/**
	 * @param string $moduleName
	 */
	public function setModuleName($moduleName){
		$this->moduleName;
	}
	
	/**
	 * @return string
	 */
	public function getModuleName(){
		return $this->moduleName;
	}
	
	public function getStorageAdapter(){
		if(!$this->storageAdapter){
			$this->storageAdapter = $this->getAsset()->getClassLoader()->newInstance(__CLASS__,__FUNCTION__,'storageAdapterClass',$this->getModuleName(),array(
				'Module' => $this
			));
			$this->getAsset()->getClassLoader()->get('EventManager')->attach('BricksConfig::set',function(EventInterface $e) use($this){
				$params = $e->getParams();
				if('BricksAsset' != $params['module'] || $this->getModuleName() != $params['namespace']){
					return;
				}
				$parts = explode('.',$params['path']);
				$name = array_pop($parts);
				if('storageAdapter'==$name){
					$this->storageAdapter = null;
				} 
			});
		}
		return $this->storageAdapter;
	}
	
	/**
	 * @return \Bricks\Asset\PublishStrategy\PublishStrategyInterface
	 */
	public function getPublishStrategy(){
		if(!$this->publishStrategy){
			$this->publishStrategy = $this->getAsset()->getClassLoader()->newInstance(__CLASS__,__METHOD__,'publishStrategyClass',$this->getModuleName(),array(
				'Module' => $this
			));
			$this->getAsset()->getClassLoader()->get('EventManager')->attach('BricksConfig::set',function(EventInterface $e) use($this){
				$params = $e->getParams();
				if('BricksAsset' != $params['module'] || $this->getModuleName() != $params['namespace']){
					return;
				}
				$parts = explode('.',$params['path']);
				$name = array_pop($parts);
				if('publishStrategy'==$name){
					$this->publishStrategy = null;
				}
			});
		}
		return $this->publishStrategy;
	}
	
	/**
	 * @return \Bricks\Asset\LessStrategy\LessStrategyInterface
	 */
	public function getLessStrategy(){
		if(!$this->lessStrategy){
			$this->lessStrategy = $this->getAsset()->getClassLoader()->newInstance(__CLASS__,__METHOD__,'lessStrategyClass',$this->getModuleName(),array(
				'Module' => $this
			));
			$this->getAsset()->getClassLoader()->get('EventManager')->attach('BricksConfig::set',function(EventInterface $e) use($this){
				$params = $e->getParams();
				if('BricksAsset' != $params['module'] || $this->getModuleName() != $params['namespace']){
					return;
				}
				$parts = explode('.',$params['path']);
				$name = array_pop($parts);
				if('lessStrategy'==$name){
					$this->lessStrategy = null;
				}
			});
		}
		return $this->lessStrategy;
	}
	
	/**
	 * @return \Bricks\Asset\ScssStrategy\ScssStrategyInterface
	 */
	public function getScssStrategy(){
		if(!$this->scssStrategy){
			$this->scssStrategy = $this->getAsset()->getClassLoader()->newInstance(__CLASS__,__METHOD__,'scssStrategyClass',$this->getModuleName(),array(
				'Module' => $this
			));
			$this->getAsset()->getClassLoader()->get('EventManager')->attach('BricksConfig::set',function(EventInterface $e) use($this){
				$params = $e->getParams();
				if('BricksAsset' != $params['module'] || $this->getModuleName() != $params['namespace']){
					return;
				}
				$parts = explode('.',$params['path']);
				$name = array_pop($parts);
				if('scssStrategy'==$name){
					$this->scssStrategy = null;
				}
			});
		}
		return $this->scssStrategy;
	}
	
	/**
	 * @return \Bricks\Asset\MinifyCssStrategy\MinifyCssStrategyInterface
	 */
	public function getMinifyCssStrategy(){
		if(!$this->minifyCssStrategy){
			$this->minifyCssStrategy = $this->getAsset()->getClassLoader()->newInstance(__CLASS__,__METHOD__,'minifyCssStrategyClass',$this->getModuleName(),array(
				'Module' => $this
			));
		}
		return $this->minifyCssStrategy;
	}
	
	/**
	 * @return \Bricks\Asset\MinifyJsStrategy\MinifyJsStrategyInterface
	 */
	public function getMinifyJsStrategy(){
		if(!$this->minifyJsStrategy){
			$this->minifyJsStrategy = $this->getAsset()->getClassLoader()->newInstance(__CLASS__,__METHOD__,'minifyJsStrategyClass',$this->getModuleName(),array(
				'Module' => $this
			));
			$this->getAsset()->getClassLoader()->get('EventManager')->attach('BricksConfig::set',function(EventInterface $e) use($this){
				$params = $e->getParams();
				if('BricksAsset' != $params['module'] || $this->getModuleName() != $params['namespace']){
					return;
				}
				$parts = explode('.',$params['path']);
				$name = array_pop($parts);
				if('minifyStrategy'==$name){
					$this->minifyStrategy = null;
				}
			});
		}
		return $this->minifyJsStrategy;
	}
	
	/**
	 * @return \Bricks\Asset\RemoveStrategy\RemoveStrategyInterface
	 */
	public function getRemoveStrategy(){
		if(!$this->removeStrategy){
			$this->removeStrategy = $this->getAsset()->getClassLoader()->newInstance(__CLASS__,__METHOD__,'removeStrategyClass',$this->getModuleName(),array(
				'Module' => $this
			));
			$this->getAsset()->getClassLoader()->get('EventManager')->attach('BricksConfig::set',function(EventInterface $e) use($this){
				$params = $e->getParams();
				if('BricksAsset' != $params['module'] || $this->getModuleName() != $params['namespace']){
					return;
				}
				$parts = explode('.',$params['path']);
				$name = array_pop($parts);
				if('removeStrategy'==$name){
					$this->removeStrategy = null;
				}
			});
		}
		return $this->removeStrategy;
	}
	
	public function publish(){
		$publish = $this->getPublishStrategy();
		$publish->publish();		
		if($this->getAsset()->getConfig()->get('lessSupport',$this->getModuleName())){
			$strategy = $this->getLessStrategy();
			$strategy->less();			
		}
		if($this->getAsset()->getConfig()->get('scssSupport',$this->getModuleName())){
			$strategy = $this->getScssStrategy();
			$strategy->scss();
		}
	}
	
	public function optimize(){
		if($this->getAsset()->getConfig()->get('minifyCssSupport',$this->getModuleName())){
			$strategy = $this->getMinifyCssStrategy();
			$strategy->minify();			
		}
		if($this->getAsset()->getConfig()->get('minifyJsSupport',$this->getModuleName())){
			$strategy = $this->getMinifyJsStrategy();
			$strategy->minify();			
		}		
	}
	
	public function remove(){
		$remove = $this->getRemoveStrategy();
		$remove->remove();
	}
	
}