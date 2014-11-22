<?php

return array(
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
			'minifyCssSupport' => false,
			'minifyJsSupport' => false,
			'lessSupport' => false,
			'scssSupport' => false,			
			'httpAssetsPath' => 'module',			
			'wwwRootPath' => dirname(__DIR__).'/httpdocs',
			'autoPublish' => false,
			'autoOptimize' => false,
			'moduleSpecific' => array(
				'BricksAsset' => array(
					'moduleAssetsPath' => dirname(__DIR__).'/public',					
				),
			),
		),
	),	
);