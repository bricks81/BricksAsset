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
			'minifyCssSupport' => true,
			'minifyJsSupport' => true,
			'lessSupport' => true,
			'scssSupport' => true,			
			'http_assets_path' => 'module',			
			'wwwroot_path' => dirname(__DIR__).'/httpdocs',
			'module_specific' => array(
				'BricksAsset' => array(
					'module_asset_path' => dirname(__DIR__).'/public',					
				),
			),
		),
	),
	'di' => array(
		'definition' => array(
			'class' => array(
				'Bricks\AssetService\AssetService' => array(
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
								'type' => 'Bricks\AssetService\Adapter\AdapterInterface',
								'required' => true,
							),			
							'required' => true,
						),
					),
				),	
				'Bricks\AssetService\AssetModule' => array(
					'methods' => array(
						'__construct' => array(
							array(
								'type' => false,
								'required' => true
							),
							array(
								'type' => false,
								'required' => true
							),							
						),
					),
				),				
				'Bricks\AssetService\PublishStrategy\CopyStrategy' => array(
					'methods' => array(
						'__construct' => array(
							array(
								'type' => 'Bricks\AssetService\Adapter\AdapterInterface',
								'required' => false,
							),
							'required' => true,
						),
					),
				),
				'Bricks\AssetService\LessStrategy\NeilimeLessphpStrategy' => array(
					'methods' => array(
						'__construct' => array(
							array(
								'type' => 'Bricks\AssetService\Adapter\AdapterInterface',
								'required' => false,
							),
							'required' => true,
						),
					),
				),
				'Bricks\AssetService\ScssStrategy\LeafoScssphpStrategy' => array(
					'methods' => array(
						'__construct' => array(
							array(
								'type' => 'Bricks\AssetService\Adapter\AdapterInterface',
								'required' => false,
							),
							'required' => true,
						),
					),
				),
				'Bricks\AssetService\MinifyCssStrategy\MrclayMinifyStrategy' => array(
					'methods' => array(
						'__construct' => array(
							array(
								'type' => 'Bricks\AssetService\Adapter\AdapterInterface',
								'required' => false,
							),
							'required' => true,
						),
					),
				),
				'Bricks\AssetService\MinifyJsStrategy\MrclayMinifyStrategy' => array(
					'methods' => array(
						'__construct' => array(
							array(
								'type' => 'Bricks\AssetService\Adapter\AdapterInterface',
								'required' => false,
							),
							'required' => true,
						),
					),
				),
			),
		),		
		/*		
		'instance' => array(
	        'Bricks\AssetService\AssetService' => array(
	        	'parameters' => array(
	        		array(
	        			'minifyCssSupport' => true,
						'minifyJsSupport' => true,
						'lessSupport' => true,
						'scssSupport' => true,			
						'wwwroot_path' => dirname(__DIR__).'/httpdocs',
						'http_assets_path' => 'module',
	        			'module_specifiy' => array(
	        				'BricksAsset' => array(
	        					'module_assets_path' => dirname(__DIR__).'/public',
	        				),
	        			),	        			
	        		),
	        		array(
	        			'BricksAsset',
	        		),
	        		'Bricks\AssetService\Adapter\FileAdapter',       		   		    		     			
	        	),
	       	),
	       	'Bricks\AssetService\PublishStrategy\CopyStrategy' => array(
	       		'parameters' => array(
	       			'Bricks\AssetService\Adapter\FileAdapter'	       			
	       		),
	       	),
	       	'Bricks\AssetService\LessStrategy\NeilimeLessphpStrategy' => array(
	       		'parameters' => array(
	       			'Bricks\AssetService\Adapter\FileAdapter',
	       		),
	       	),
	       	'Bricks\AssetService\ScssStrategy\LeafoScssphpStrategy' => array(
	       		'parameters' => array(
	       			'Bricks\AssetService\Adapter\FileAdapter',
	       		),
	       	),
	       	'Bricks\AssetService\MinifyCssStrategy\MrclayMinifyStrategy' => array(
	       		'parameters' => array(
	       			'Bricks\AssetService\Adapter\FileAdapter',
	       		),
	       	),
	       	'Bricks\AssetService\MinifyJsStrategy\MrclayMinifyStrategy' => array(
	       		'parameters' => array(
	       			'Bricks\AssetService\Adapter\FileAdapter',
	       		),
	       	),
	    ),
	    */	    
	),	
);