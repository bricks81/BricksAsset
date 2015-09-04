<?php

return array(	
	'service_manager' => array(
		'factories' => array(
			'BricksAsset' => 'Bricks\Asset\ServiceManager\AssetFactory',
		),				
	),
	'invokables' => array(
		'Resource' => 'Bricks\View\Helper\Resource',
	),
	'BricksConfig' => array(
		'BricksClassLoader' => array(
			'BricksClassLoader' => array(
				'classMap' => array(
					'BricksAsset' => array(
						'BricksAsset' => array(
							'assetClass' => 'Bricks\Asset\Asset',
							'assetModule' => 'Bricks\Asset\Module',
							'storageAdapter' => 'Bricks\Asset\AssetAdapter\FilesystemAdapter',
							'lessStrategy' => 'Bricks\Asset\LessStrategy\OyejorgeLessphpStrategy',
							'scssStrategy' => 'Bricks\Asset\ScssStrategy\LeafoScssphpStrategy',
							'minifyCssStrategy' => 'Bricks\Asset\MinifyCssStrategy\MrclayMinifyStrategy',
							'minifyJsStrategy' => 'Bricks\Asset\MinifyJsStrategy\MrclayMinifyStrategy',
							'publishStrategy' => 'Bricks\Asset\PublishStrategy\CopyStrategy',
							'removeStrategy' => 'Bricks\Asset\RemoveStrategy\RemoveStrategy',
						),
					),					
				),
			),
		),
		'BricksAsset' => array(
			'BricksAsset' => array(				
				'minifyCssSupport' => false,
				'minifyJsSupport' => false,
				'lessSupport' => false,
				'scssSupport' => false,
				'httpAssetsPath' => 'module',
				'wwwRootPath' => './public',
				'autoPublish' => false,
				'autoOptimize' => false,
				'moduleAssetsPath' => dirname(__DIR__).'/public',
				'publishExclude' => array(),
				'removeExclude' => array(),				
			),
		),
		'BricksPlugin' => array(
			'BricksPlugin' => array(
				'extend' => array(
					'Zend\View\Helper\HeadLink' => array(
						'Bricks\Asset\View\Helper\HeadLink',
					),
					'Zend\View\Helper\HeadScript' => array(
						'Bricks\Asset\View\Helper\HeadScript',
					),
				),
				'listeners' => array(
					'Zend\View\Helper\HeadLink::itemToString.pre' => array(
						'Bricks\Asset\View\Helper\HeadLink::preItemToString' => -100000
					),					
				)
			),
		),
	),	
);
