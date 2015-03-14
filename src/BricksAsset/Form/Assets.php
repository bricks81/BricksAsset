<?php

namespace BricksAsset\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Form\Fieldset;

class Assets extends Form {
	
	public function setupElements($loadedModules){		
		$modules = array();
		foreach($loadedModules AS $moduleName){
			$modules[$moduleName] = $moduleName;
		}

		// poedit translation
		function _($str){return $str;}
		
		$element = new Element\MultiCheckbox();
		$element->setName('modules');
		$element->setLabel('Select which modules will be affected');
		$element->setValueOptions($modules);
		$this->add($element);
		
		$element = new Element\MultiCheckbox();
		$element->setName('actions');
		$element->setLabel('Which action to take');
		$element->setValueOptions(array(
			'remove' => _('Remove assets'),
			'publish' => _('Publish assets'),
			'optimize' => _('Optimize assets'),
		));
		$this->add($element);
		
		$element = new Element\Radio();
		$element->setName('lessSupport');
		$element->setLabel('Less Publish Options');
		$element->setValueOptions(array(
			'default' => _('Use modules config'),
			'enable' => _('Force parsing less'),
			'disable' => _('Avoid parsing less'),
		));
		$element->setValue('default');
		$this->add($element);
		
		$element = new Element\Radio();
		$element->setName('scssSupport');
		$element->setLabel('Scss Publish Options');
		$element->setValueOptions(array(
			'default' => _('Use modules config'),
			'enable' => _('Force parsing scss'),
			'disable' => _('Avoid parsing scss'),
		));
		$element->setValue('default');
		$this->add($element);
		
		$element = new Element\Radio();
		$element->setName('minifyCssSupport');
		$element->setLabel('CSS Optimize Options');
		$element->setValueOptions(array(
			'default' => _('Use modules config'),
			'enable' => _('Force minifying CSS'),
			'disable' => _('Avoid minifying CSS'),
		));
		$element->setValue('default');
		$this->add($element);
		
		$element = new Element\Radio();
		$element->setName('minifyJsSupport');
		$element->setLabel('JS Optimize Options');
		$element->setValueOptions(array(
			'default' => _('Use modules config'),
			'enable' => _('Force minifying js'),
			'disable' => _('Avoid minifying js'),
		));
		$element->setValue('default');
		$this->add($element);
		
		$fieldset = new Fieldset('submit');
		$fieldset->setAttribute('class','submit');
		$element = new Element\Submit('submitData');
		$element->setValue(_('Submit now'));
		$fieldset->add($element);
		$this->add($fieldset);
	} 
	
}