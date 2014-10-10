<?php

namespace BricksAsset\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AssetController extends AbstractActionController {
	
	public function indexAction(){		
	}
	
	public function publishAction(){
	}
	
	public function publishDoAction(){
		$this->publish();
		$this->optimize();
		$this->redirect()->toRoute('bricksasset-publish-success');		
	}
	
	public function publishSuccessAction(){		
	}
	
	public function publish(array $moduleList=array()){
		$as = $this->getServiceLocator()->get('Bricks\AssetService');
		$as->publish($moduleList);
	}
	
	/*
	public function optimizeAction(){		
	}
	
	public function optimizeDoAction(){
		$this->optimize();
		$this->redirect()->toRoute('bricksasset-optimize-success');
	}
	
	public function optimizeSuccessAction(){
	}
	*/
	
	public function optimize(array $moduleList=array()){
		$as = $this->getServiceLocator()->get('Bricks\AssetService');
		$as->optimize($moduleList);
	}
	
} 