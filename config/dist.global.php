<?php

return array(
	'view_helpers' => array(
		'invokables' => array(
			'HeadLink' => 'Bricks\View\Helper\HeadLink',
			'HeadScript' => 'Bricks\View\Helper\HeadScript',
		),
	),
	'Bricks' => array(
		'BricksAsset' => array(
			'autoPublish' => true,
			'autoOptimize' => true,
			'minifyCssSupport' => true,
			'minifyJsSupport' => true,
			'lessSupport' => true,
			'scssSupport' => true,
			'module_assets_path' => './public',
			'wwwroot_path' => './public',
			'http_assets_path' => 'module',
			'module_specific' => array(
			),				
		),
	),
	'router' => array(
		'router_class' => 'Zend\Mvc\Router\Http\TranslatorAwareTreeRouteStack',
		'routes' => array(
			'bricksasset' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/{route-bricksasset}',
					'defaults' => array(
						'controller' => 'BricksAsset\Controller\AssetController',
						'action'     => 'index',
					),
				),
			),
			'bricksasset-publish' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/{route-bricksasset-publish}',
					'defaults' => array(
						'controller' => 'BricksAsset\Controller\AssetController',
						'action'     => 'publish',
					),
				),
			),
			'bricksasset-publish-do' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/{route-bricksasset-publish-do}',
					'defaults' => array(
						'controller' => 'BricksAsset\Controller\AssetController',
						'action'     => 'publishDo',
					),
				),
			),
			'bricksasset-publish-success' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/{route-bricksasset-publish-success}',
					'defaults' => array(
						'controller' => 'BricksAsset\Controller\AssetController',
						'action'     => 'publishSuccess',
					),
				),
			),			
		),
	),
);