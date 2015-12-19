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
		'__DEFAULT_NAMESPACE__' => array(
			'BricksClassLoader' => array(
				'aliasMap' => array(
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
			'BricksPlugin' => array(
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
					'Zend\View\Helper\HeadLink::itemToString.post' => array(
						'Bricks\Asset\View\Helper\HeadLink::postItemToString' => -100000
					),
					'Zend\View\Helper\HeadScript::itemToString.post' => array(
						'Bricks\Asset\View\Helper\HeadScript::postItemToString' => -100000
					),
					'Zend\View\Helper\InlineScript::itemToString.post' => array(
						'Bricks\Asset\View\Helper\InlineScript::postItemToString' => -100000
					),
				)
			),
		),
	),	
);
