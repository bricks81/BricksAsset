<?php

return array(	
	'service_manager' => array(
		'factories' => array(
			'BricksAsset' => 'Bricks\Asset\ServiceManager\AssetFactory',
		),				
	),
	'invokables' => array(
		'Resource' => 'Bricks\Asset\View\Helper\Resource',
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
			'BricksAsset' => array(
				'extend' => array(
					'Zend\View\Helper\HeadLink' => array(
						'Bricks\Asset\View\Helper\HeadLink',
					),
					'Zend\View\Helper\HeadScript' => array(
						'Bricks\Asset\View\Helper\HeadScript',
					),
					'Zend\View\Helper\InlineScript' => array(
						'Bricks\Asset\View\Helper\InlineScript',						
					),
				),
				'listeners' => array(
					'Zend\View\Helper\HeadLink::itemToString.pre' => array(
						'Bricks\Asset\View\Helper\HeadLink::preItemToString' => -100000
					),					
					'Zend\View\Helper\HeadScript::itemToString.pre' => array(
						'Bricks\Asset\View\Helper\HeadScript::preItemToString' => -100000
					),
					'Zend\View\Helper\InlineScript::itemToString.pre' => array(
						'Bricks\Asset\View\Helper\InlineScript::preItemToString' => -100000
					),
				)
			),
		),
	),	
);
