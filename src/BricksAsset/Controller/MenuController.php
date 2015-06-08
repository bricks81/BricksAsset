<?php
/**
 * Bricks Framework & Bricks CMS
 *
 * @link https://github.com/bricks81/BricksAsset
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace BricksAsset\Controller;

use Bricks\Menu\ToolbarItem;
use Bricks\Menu\ToolbarGroup;
use Bricks\Menu\ToolbarSet;
use Bricks\Menu\MenuItem;
use Bricks\Menu\MenuIcon;
use Bricks\Menu\Menu;
use Zend\Mvc\Controller\AbstractActionController;

class MenuController extends AbstractActionController {
	
	/*
	public function addDefaultMenu(){
		if(!$this->getServiceLocator()->has('Bricks\MenuService')){
			return;
		}
		$menus = $this->getServiceLocator()->get('Bricks\MenuService');
		$router = $this->getServiceLocator()->get('Router');
		$router->setBaseUrl($this->getServiceLocator()->get('Request')->getBaseUrl());
		$translator = $this->getServiceLocator()->get('MvcTranslator');
		
		if(!$menus->hasMenu('default')){
			return;
		}
		
		$menu = new Menu('default',1);
		$menuItem = new MenuItem('menuit-admin',1);
		$menuItem->setTitle($translator->translate('Administration'));
		$toolbarSet = new ToolbarSet('tbset-admin');
		$toolbarGroup = new ToolbarGroup('tbgrp-admin-assets',2);
		$toolbarGroup->setSubtitle($translator->translate('Assets'));
		
		$toolbarItem = new ToolbarItem('tbitem-admin-general-publish',1);
		$toolbarItem->setTitle($translator->translate('Publish assets'));
		$toolbarItem->setUrl($router->assemble(array(),array('name'=>'bricksasset-publish')));
		$menuIcon = new MenuIcon();
		$menuIcon->setIconString('<i class="fa fa-2x fa-photo"></i>');
		$toolbarItem->setMenuIcon($menuIcon);
		$toolbarGroup->addItem($toolbarItem);
		
		$toolbarSet->addGroup($toolbarGroup);
		$menuItem->setToolbarSet($toolbarSet);
		$menu->addItem($menuItem);
		$menus->mergeMenu($menu);			
	}
	*/
	
}