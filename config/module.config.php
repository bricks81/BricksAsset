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
);