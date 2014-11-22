<?php

use Bricks\Di\Di;

return array(	
	'service_manager' => array(
		'factories' => array(
			'Bricks\AssetService' => 'Bricks\AssetService\ServiceManager\AssetServiceFactory',
		),				
	),
	'di' => array(		
		'definition' => array(
			'class' => array(
				'Bricks\AssetService\AssetModule' => array(					
					'methods' => array(
						'__construct' => array(
							array(
								'type' => false,
								'required' => true,
							),
							array(								
								'type' => false,
								'required' => true,								
							),
							array(								
								'type' => false,
								'required' => true,
							),	
							'required' => true,
						),
					),
				),				
	       	),
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
			'classLoader' => 'Bricks\AssetService\ClassLoader\ClassLoader',
			'assetModule' => 'Bricks\AssetService\AssetModule',
			'assetAdapter' => 'Bricks\AssetService\AssetAdapter\FilesystemAdapter',
			'classLoader' => 'Bricks\AssetService\ClassLoader\ClassLoader',
			'lessStrategy' => 'Bricks\AssetService\LessStrategy\NeilimeLessphpStrategy',
			'scssStrategy' => 'Bricks\AssetService\ScssStrategy\LeafoScssphpStrategy',
			'minifyCssStrategy' => 'Bricks\AssetService\MinifyCssStrategy\MrclayMinifyStrategy',
			'minifyJsStrategy' => 'Bricks\AssetService\MinifyJsStrategy\MrclayMinifyStrategy',
			'publishStrategy' => 'Bricks\AssetService\PublishStrategy\CopyStrategy',
			'removeStrategy' => 'Bricks\AssetService\RemoveStrategy\RemoveStrategy',
			'minifyCssSupport' => true,
			'minifyJsSupport' => true,
			'lessSupport' => true,
			'scssSupport' => true,
			'wwwRootPath' => './public',
			'httpAssetsPath' => 'module',
			'moduleSpecific' => array(
				/*
				'BricksAsset' => array(
					'moduleAssetsPath' => dirname(__DIR__).'/public',
				),
				*/								
			),			
		),
	),	
);