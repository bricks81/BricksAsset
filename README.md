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
## Installation
### Using Composer
    php composer.phar require bricks81/BricksAsset
### Download as Module
First Download bricks81/BricksFile.
Load this package and install both under module/BricksFile and module/BricksAsset.
### Activate Modules
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
### Example of all Features
This ist the default configuration of BricksAsset and has not to be defined extra.
 
	// ...
	'bricks' => array(
		// ...
		'BricksAsset' => array(
			'autoPublish' => false,
			'autoOptimize' => false,			
			'MinifyCssSupport' => false, // false for faster development
			'LessSupport' => true, // if you got a scss webserver module you could deactivate this
			'ScssSupport' => true, // if you got a scss webserver module you could deactivate this
			'MinifyJsSupport' => false, // false for faster development
			'publishAdapter' => 'Bricks\AssetService\PublishAdapter\CopyAdapter',			
			'lessAdapter' => 'Bricks\AssetService\LessAdapter\NeilimeLessphpAdapter',
			'scssAdapter' => 'Bricks\AssetService\ScssAdapter\LeafoScssphpAdapter',
			'minifyAdapter' => 'Bricks\AssetService\MinifyAdapter\MrclayMinifyAdapter',
			'module_assets_path' => './public',
			'wwwroot_path' => './public',
			'http_assets_path' => 'module',			
		),
		// ...
	),
	// ...   

Meaning of this parts
	
	'publishAdapter' 		specifies the publish adapter which will be used.

	'lessAdapter' 			specifies the less adapter which will be used.

	'scssAdapter'			specifies the scss/sass adapter which will be used.

	'minifyAdapter' 		specifies the minify adapter which will be used.

	'module_assets_path' 	is the relative subdirectory of the module in 
							which are files stored that have to be published.

	'wwwroot_path' 			defines the directory which is reachable through 
							your browser (path of the filesystem - can be relative).

	'http_assets_path' 		is the relative subpath to the folder where the files 
							will be copied or linked.

### Graficaly
	application dir			current working directory
	|
	|- Modules
	|	|- BricksAsset		
	|		|- public		module_assets_path (refers to the filesystem 
	|						- must be relative to the modules
	|						directory)
 	|
	|- public				wwwroot_path (refers to the filesystem 
		|					- can be relative)
		|- module			http_assets_path (refers to the http path 
							- must be relative to the www root)

### Advanced configuration
You could define module specific parameters.
	
	// ...
	'Bricks' => array(
		// ...
		'BricksAsset' => array(
			// ...
			'module_specific' => array(
				'YourModule' => array(
				 	'publishAdapter' => 'YourModule\AssetService\PublishAdapter\CopyAdapter',
					'lessAdapter' => 'Bricks\AssetService\LessAdapter\NeilimeLessphpAdapter',
					'lessAdapter' => 'Bricks\AssetService\ScssAdapter\LeafoScssphpAdapter',
					'minifyAdapter' => 'Bricks\AssetService\MinifyAdapter\MrclayMinifyAdapter',
					'module_assets_path' => './your/public',
					'wwwroot_path' => './public',
					'http_assets_path' => 'your/module/public/path',					
				),
			),
		),
		// ...
	),   
	// ...

## Usage

You can use your HeadLink and HeadScript as it is. And you could use .less, .scss and .sass files, too.

	echo $this->headLink()->appendStylsheet('module/ModuleName/css/my.less');
	echo $this->headScript()->appendFile('module/ModuleName/js/my.js');

The example above will output:

	<link href="module/ModuleName/css/my.less.css" media="screen" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="module/ModuleName/js/my.js"></script>

This works without optimization. After optimization it will be:

	<link href="module/ModuleName/css/my.less.min.css" media="screen" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="module/ModuleName/js/my.min.js"></script>

## Note

Hope you will enjoy it.