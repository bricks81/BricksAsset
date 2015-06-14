<?php

return array(
	'BricksClassLoader' => array(
		'BricksAsset' => array(
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
			),
		),
	),
	'BricksConfig' => array(
		'BricksAsset' => array(
			'BricksAsset' => array(				
				'minifyCssSupport' => false,
				'minifyJsSupport' => false,
				'lessSupport' => false,
				'scssSupport' => false,
				'httpAssetsPath' => 'module',
				'wwwRootPath' => dirname(__DIR__).'/httpdocs',
				'autoPublish' => false,
				'autoOptimize' => false,
				'moduleAssetsPath' => dirname(__DIR__).'/public',				
			),
		),
	),		
);