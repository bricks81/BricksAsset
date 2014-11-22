## About
Bricks Asset is just another Asset Service to publish
the http reachable files of a module. Simple, flexible and extendable.
## Features
- Less support
- SCSS/SASS Support
- Minify CSS & JS
- Overwrites HeadLink *(you can use it as it is known from ZF2)*

*(To get a single file please consider to use googles pagespeed module)*

## Requires
- bricks81/BricksFile
- neilime/lessphp
- mrclay/minify
- leafo/scssphp 

### Installation
#### Using Composer
    php composer.phar require bricks81/bricks-asset
#### Activate Modules
Add the modules in your application.config.php:

	// ...    
	'modules' => array(
    	// ...
    	'BricksFile',	
    	'BricksAsset',
    	'Application',
    	// ...	
    ),
	// ...

## Configuration
### what you've to do
Add one important line

	// ...
	'Bricks' => array(
		// ...
		'BricksAsset' => array(
			'moduleAssetsPath' => dirname(__DIR__).'/public',
			// activate auto publish
			'autoPublish' => true,
			// activate optimization
			'autoOptimize' => true,
		),
		// ...
	),
	// ...

After this you've to add the configuration for your module:

	// ...
	'Bricks' => array(
		// ...
		'BricksAsset' => array(
			'moduleSpecific' => array(
				'YourModule => array(
					'moduleAssetsPath' => dirname(__DIR__).'/public';
				),
			),
		),
		// ...
	),
	// ...

### Example of all Features
This ist the default configuration of BricksAsset and has not to be defined extra.
 
	// ...
	'Bricks' => array(
		// ...
		'BricksAsset' => array(
			'wwwRootPath' => './public',
			'httpAssetsPath' => 'module',
			'autoPublish' => false,
			'autoOptimize' => false,			
			'minifyCssSupport' => false, // false for faster development
			'lessSupport' => true, // if you got a scss webserver module you could deactivate this
			'scssSupport' => true, // if you got a scss webserver module you could deactivate this
			'minifyJsSupport' => false, // false for faster development
			'classLoader' => 'Bricks\AssetService\ClassLoader\ClassLoader',
			'assetModule' => 'Bricks\AssetService\AssetModule',
			'assetAdapter' => 'Bricks\AssetService\AssetAdapter\FilesystemAdapter',
			'publishStrategy' => 'Bricks\AssetService\PublishStrategy\CopyStrategy',	
			'removeStrategy' => 'Bricks\AssetService\RemoveStrategy\RemoveStrategy',
			'lessStrategy' => 'Bricks\AssetService\LessStrategy\NeilimeLessphpStrategy',
			'scssStrategy' => 'Bricks\AssetService\ScssStrategy\LeafoScssphpStrategy',
			'minifyCssStrategy' => 'Bricks\AssetService\MinifyCssStrategy\MrclayMinifyStrategy',
			'minifyJsStrategy' => 'Bricks\AssetService\MinifyJsStrategy/MrclayMinifyStrategy',
			'moduleSpecific' => array(
				'YourModule' => array(
					'wwwRootPath' => './public',
					'httpAssetsPath' => 'module',
					'autoPublish' => false,
					'autoOptimize' => false,			
					'minifyCssSupport' => false, // false for faster development
					'lessSupport' => true, // if you got a scss webserver module you could deactivate this
					'scssSupport' => true, // if you got a scss webserver module you could deactivate this
					'minifyJsSupport' => false, // false for faster development
					'classLoader' => 'Bricks\AssetService\ClassLoader\ClassLoader',
					'assetAdapter' => 'Bricks\AssetService\AssetAdapter\FilesystemAdapter',
					'publishStrategy' => 'Bricks\AssetService\PublishStrategy\CopyStrategy',	
					'removeStrategy' => 'Bricks\AssetService\RemoveStrategy\RemoveStrategy',
					'lessStrategy' => 'Bricks\AssetService\LessStrategy\NeilimeLessphpStrategy',
					'scssStrategy' => 'Bricks\AssetService\ScssStrategy\LeafoScssphpStrategy',
					'minifyCssStrategy' => 'Bricks\AssetService\MinifyCssStrategy\MrclayMinifyStrategy',
					'minifyJsStrategy' => 'Bricks\AssetService\MinifyJsStrategy/MrclayMinifyStrategy',
					'moduleAssetsPath' => dirname(__DIR__).'/public',
				),
			),
		),
		// ...
	),
	// ...   

Meaning of this parts
	
	'moduleAssetsPath'	 	is the absolute path of the modules public dir 
							which stores files that have to be published.
							This have to be defined for each Module.

	'wwwRootPath' 			defines the directory which is reachable through 
							your browser (path of the filesystem - can be relative).

	'httpAssetsPath' 		is the relative subpath to the folder where the 						files will be copied or linked.

### Graficaly
	application dir			current working directory
	|
	|- Modules
	|	|- BricksAsset		
	|		|- public		moduleAssetsPath (refers to the filesystem 
	|						- must be absolute directory path)
 	|
	|- public				wwwRootPath (refers to the filesystem 
		|					- can be relative)
		|- module			httpAssetsPath (refers to the http path 
							- must be relative to the wwwRootPath)

## Usage

You can use your HeadLink and HeadScript as it is. And you could use .less, .scss and .sass files, too.

	echo $this->headLink()->appendStylsheet('ModuleName/css/my.less');
	echo $this->headScript()->appendFile('ModuleName/js/my.js');

The example above will output:

	<link href="module/ModuleName/css/my.less.css" media="screen" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="module/ModuleName/js/my.js"></script>

This works without optimization. After optimization it will be:

	<link href="module/ModuleName/css/my.less.min.css" media="screen" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="module/ModuleName/js/my.min.js"></script>

## Note

Hope you will enjoy it.

### More see at
[https://github.com/bricks81/BricksAsset/wiki](https://github.com/bricks81/BricksAsset/wiki "BricksAsset Wiki")