<?php
use Bricks\Di\Di;

return array(	
	'service_manager' => array(
		'factories' => array(
			'BricksAsset' => 'Bricks\Asset\ServiceManager\AssetFactory',
		),				
	),
	'router' => array(
		'router_class' => 'Zend\Mvc\Router\Http\TranslatorAwareTreeRouteStack',
		'routes' => array(			
			'bricks.asset.index' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/{Admin}/{Assets}',
					'defaults' => array(
						'controller' => 'BricksAsset\Controller\AssetController',
						'action'     => 'index',
					),
				),
			),
			'bricks.asset.publish' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/{Admin}/{Assets/Write}',
					'defaults' => array(
						'controller' => 'BricksAsset\Controller\AssetController',
						'action'     => 'publish',
					),	
				),
			),
			'bricks.asset.publish.do' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/{Admin}/{Assets/Write/Execute}',
					'defaults' => array(
						'controller' => 'BricksAsset\Controller\AssetController',
						'action'     => 'publishDo',
					),
				),
			),
			'bricks.asset.publish.success' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/{Admin}/{Assets/Write/Success}',
					'defaults' => array(
						'controller' => 'BricksAsset\Controller\AssetController',
						'action'     => 'publishSuccess',
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
			array(
				'type' => 'gettext',
				'base_dir' => __DIR__.'/../language',
				'pattern' => '%1$s.mo',
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
	'BricksClassLoader' => array(
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
	'BricksConfig' => array(
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
			'BricksAssetSelf' => array(
				'scssSupport' => true
			),
		),
	),	
)



;