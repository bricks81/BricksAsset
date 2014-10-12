<?php

return array(
	'service_manager' => array(
		'factories' => array(
			'Bricks\AssetService' => 'Bricks\AssetService\AssetService',
		),				
	),
	'controllers' => array(
		'invokables' => array(
			'BricksAsset\Controller\AssetController' => 'BricksAsset\Controller\AssetController',
			'BricksAsset\Controller\MenuController' => 'BricksAsset\Controller\MenuController',
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
		'delegators' => array(
			'HeadLink' => array(
				'Bricks\View\Helper\HeadLinkDelegator',
			),
			'HeadScript' => array(
				'Bricks\View\Helper\HeadScriptDelegator',
			),
		),
	),
	'Bricks' => array(
		'BricksAsset' => array(
			'autoPublish' => false,
			'autoOptimize' => false,
			'minifyCssSupport' => false,
			'minifyJsSupport' => false,
			'lessSupport' => false,
			'scssSupport' => false,
			'module_assets_path' => './public',
			'wwwroot_path' => './public',
			'http_assets_path' => 'module',
			'module_specific' => array(
			),
		),
	),	
);