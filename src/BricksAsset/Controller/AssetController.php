<?php

namespace BricksAsset\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Form;
use Zend\Form\Element;

class AssetController extends AbstractActionController {
	
	public function indexAction(){		
	}
	
	public function publishAction(){
		$form = $this->getPublishForm();
		return array('publishForm'=>$form);
	}
	
	public function publishDoAction(){
		$modules = array();
		if($this->getRequest()->getPost('modules')){
			foreach($this->getRequest()->getPost('modules') AS $module){
				$modules[] = $module;
			}
		}	
		if($this->getRequest()->getPost('remove')){			
			$this->remove($modules);
		}				
		$this->publish($modules);
		if($this->getRequest()->getPost('optimize')){
			$this->optimize($modules);
		}
		
		$this->redirect()->toRoute('bricksasset-publish-success');		
	}
	
	public function publishSuccessAction(){		
	}
	
	public function publish(array $moduleList=array()){
		$as = $this->getServiceLocator()->get('Bricks\AssetService');
		$as->publish($moduleList);
	}
	
	public function optimize(array $moduleList=array()){
		$as = $this->getServiceLocator()->get('Bricks\AssetService');
		$as->optimize($moduleList);
	}
	
	public function remove(array $moduleList=array()){
		$as = $this->getServiceLocator()->get('Bricks\AssetService');
		$as->remove($moduleList);
	}
	
	public function getPublishForm(){
		$modules = array();
		$appcfg = $this->getServiceLocator()->get('ApplicationConfig');		
		if(isset($appcfg['modules'])){
			foreach($appcfg['modules'] AS $module){
				$modules[$module] = $module;
			}			
		}
		
		$t = $this->getServiceLocator()->get('MvcTranslator');
		
		$form = new Form();
		$form->setAttribute('action',$this->Url()->fromRoute('bricksasset-publish-do'));		
		
		$element = new Element\MultiCheckbox();
		$element->setName('modules');
		$element->setLabel($t->translate('bricksasset-form-publish-label-modules'));
		$element->setValueOptions($modules);				
		$form->add($element);
		
		$element = new Element\Checkbox();
		$element->setName('remove');
		$element->setLabel($t->translate('bricksasset-form-publish-label-remove'));
		$form->add($element);
		
		$element = new Element\Checkbox();
		$element->setName('update');
		$element->setLabel($t->translate('bricksasset-form-publish-label-update'));
		$form->add($element);
		
		$element = new Element\Checkbox();
		$element->setName('optimize');
		$element->setLabel($t->translate('bricksasset-form-publish-label-optimize'));
		$form->add($element);
		
		$element = new Element\Submit('submitData');
		$element->setValue($t->translate('bricksasset-form-publish-submit'));
		$form->add($element);
		
		return $form;
	}
	
} 