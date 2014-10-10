<?php

return array(
	'view_helpers' => array(
		'invokables' => array(
			'HeadLink' => 'Bricks\View\Helper\HeadLink',
			'HeadScript' => 'Bricks\View\Helper\HeadScript',
		),
	),
	'Bricks' => array(
		'BricksAsset' => array(
			'autoPublish' => true,
			'autoOptimize' => true,
			'minifyCssSupport' => true,
			'minifyJsSupport' => true,
			'lessSupport' => true,
			'scssSupport' => true,
			'module_assets_path' => './public',
			'wwwroot_path' => './public',
			'http_assets_path' => 'module',
			'module_specific' => array(
			),				
		),
	),
);