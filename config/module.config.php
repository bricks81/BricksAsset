<?php

return array(
	'service_manager' => array(
		'factories' => array(
			'Bricks\AssetService' => 'Bricks\AssetService\AssetService',
		),				
	),
	'controllers' => array(
		'invokables' => array(
			//'BricksAsset\Controller\AssetController' => 'BricksAsset\Controller\AssetController',
			//'BricksAsset\Controller\MenuController' => 'BricksAsset\Controller\MenuController',
		),
	),
	'translator' => array(
		'translation_file_patterns' => array(
			array(
				'type' => 'phparray',
				'base_dir' => __DIR__.'/../language',
				'pattern' => '%1$s/%1$s.php',
			),
		),
	),
	'view_manager' => array(
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
	'view_helpers' => array(
		'invokables' => array(
			'HeadLink' => 'Bricks\View\Helper\HeadLink',
			'HeadScript' => 'Bricks\View\Helper\HeadScript',
		),
	),	
	'router' => array(
		'router_class' => 'Zend\Mvc\Router\Http\TranslatorAwareTreeRouteStack',
		/*
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
			'bricksasset-optimize' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/{route-bricksasset-optimize}',
					'defaults' => array(
						'controller' => 'BricksAsset\Controller\AssetController',
						'action'     => 'optimize',
					),
				),
			),
			'bricksasset-optimize-do' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/{route-bricksasset-optimize-do}',
					'defaults' => array(
						'controller' => 'BricksAsset\Controller\AssetController',
						'action'     => 'optimizeDo',
					),
				),
			),
			'bricksasset-optimize-success' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/{route-bricksasset-optimize-success}',
					'defaults' => array(
						'controller' => 'BricksAsset\Controller\AssetController',
						'action'     => 'optimizeSuccess',
					),
				),
			),			
		),
		*/
	),
	'Bricks' => array(
		'BricksAsset' => array(
			'autoPublish' => false,
			'autoOptimize' => false,
			'minifyCssSupport' => false,
			'minifyJsSupport' => false,
			'lessSupport' => false,
			'scssSupport' => false,					
			'publishAdapter' => 'Bricks\AssetService\PublishAdapter\CopyAdapter',				
			'lessAdapter' => 'Bricks\AssetService\LessAdapter\NeilimeLessphpAdapter',
			'scssAdapter' => 'Bricks\AssetService\ScssAdapter\LeafoScssphpAdapter',
			'minifyAdapter' => 'Bricks\AssetService\MinifyAdapter\MrclayMinifyAdapter',				
			'module_assets_path' => './public',
			'wwwroot_path' => './public',
			'http_assets_path' => 'module',						
			'module_specific' => array(			
			),
		),
	),
);