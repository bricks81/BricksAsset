<?php

use Bricks\Di\Di;

return array(	
	'service_manager' => array(
		'factories' => array(
			'Bricks\Asset' => 'Bricks\Asset\ServiceManager\AssetServiceFactory',
		),				
	),
	'di' => array(		
		'definition' => array(
			'class' => array(
				'Bricks\Asset\AssetModule' => array(					
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
		'invokables' => array(
			'Resource' => 'Bricks\View\Helper\Resource',
		),
	),
	'Bricks' => array(
		'BricksAsset' => array(
			'classLoader' => 'Bricks\Asset\ClassLoader\ClassLoader',
			'assetModule' => 'Bricks\Asset\AssetModule',
			'assetAdapter' => 'Bricks\Asset\AssetAdapter\FilesystemAdapter',
			'classLoader' => 'Bricks\Asset\ClassLoader\ClassLoader',
			'lessStrategy' => 'Bricks\Asset\LessStrategy\OyejorgeLessphpStrategy',
			'scssStrategy' => 'Bricks\Asset\ScssStrategy\LeafoScssphpStrategy',
			'minifyCssStrategy' => 'Bricks\Asset\MinifyCssStrategy\MrclayMinifyStrategy',
			'minifyJsStrategy' => 'Bricks\Asset\MinifyJsStrategy\MrclayMinifyStrategy',
			'publishStrategy' => 'Bricks\Asset\PublishStrategy\CopyStrategy',
			'removeStrategy' => 'Bricks\Asset\RemoveStrategy\RemoveStrategy',
			'minifyCssSupport' => true,
			'minifyJsSupport' => true,
			'lessSupport' => true,
			'scssSupport' => true,
			'wwwRootPath' => './public',
			'httpAssetsPath' => 'module',
			'moduleSpecific' => array(
				'BricksAsset' => array(
					'moduleAssetsPath' => dirname(__DIR__).'/public',					
				),												
			),			
		),
	),	
);