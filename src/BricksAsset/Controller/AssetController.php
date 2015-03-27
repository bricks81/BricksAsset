<?php

namespace BricksAsset\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use BricksAsset\Form\Assets;
use Zend\Mvc\MvcEvent;

class AssetController extends AbstractActionController {
	
	public function indexAction(){		
	}
	
	public function publishAction(){
		$form = $this->getPublishForm();
		$form->setAttribute('action',$this->Url()->fromRoute('bricks.asset.publish.do'));
		$as = $this->getServiceLocator()->get('Bricks.Asset.AssetService');
		$loadedModules = $as->getLoadedModules();
		$form->setupElements($loadedModules);
		return array('publishForm'=>$form);
	}
	
	public function publishDoAction(){
		$modules = array();
		if($this->getRequest()->getPost('modules')){
			foreach($this->getRequest()->getPost('modules') AS $module){
				$modules[] = $module;
			}
		}
		$remove = false;
		$publish = false;
		$optimize = false;
		if($this->getRequest()->getPost('actions')){
			if(in_array('remove',$this->getRequest()->getPost('actions'))){
				$remove = true;
			}
			if(in_array('publish',$this->getRequest()->getPost('actions'))){
				$publish = true;
			}
			if(in_array('optimize',$this->getRequest()->getPost('actions'))){
				$optimize = true;
			}
		}			

		$lessSupport = $this->getRequest()->getPost('lessSupport')?:'default';
		$scssSupport = $this->getRequest()->getPost('scssSupport')?:'default';
		$minifyCssSupport = $this->getRequest()->getPost('minifyCssSupport')?:'default';
		$minifyJsSupport = $this->getRequest()->getPost('minifyJsSupport')?:'default';
		
		$as = $this->getServiceLocator()->get('Bricks.Asset.AssetService');
		foreach($modules AS $moduleName){
			$module = $as->getModule($moduleName);
			$origLessSupport = $module->getLessSupport();
			$origScssSupport = $module->getScssSupport();
			$origMinifyCssSupport = $module->getMinifyCssSupport();
			$origMinifyJsSupport = $module->getMinifyJsSupport();
			if('default'!=$lessSupport){				
				$module->setLessSupport('enable'==$lessSupport?:false);
			}
			if('default'!=$scssSupport){				
				$module->setScssSupport('enable'==$scssSupport?:false);
			}
			if('default'!=$minifyCssSupport){
				$module->setMinifyCssSupport('enable'==$minifyCssSupport?:false);
			}
			if('default'!=$minifyJsSupport){
				$module->setMinifyJsSupport('enable'==$minifyJsSupport?:false);
			}
			if($remove){
				$module->remove();
			}
			if($publish){
				$module->publish();
			}
			if($optimize){
				$module->optimize();
			}
			$module->setLessSupport($origLessSupport);
			$module->setScssSupport($origScssSupport);
			$module->setMinifyCssSupport($origMinifyCssSupport);
			$module->setMinifyJsSupport($origMinifyJsSupport);
		}
		
		$this->redirect()->toRoute('bricks.asset.publish.success');		
	}
	
	public function publishSuccessAction(){		
	}
	
	public function getPublishForm(){
		return new Assets();
	}
	
} 