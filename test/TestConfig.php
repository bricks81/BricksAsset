<?php
return array(
    'modules' => array(
    	'BricksDi',
    	'BricksFile',    	
    	'BricksAsset',        
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../../config/autoload/{,*.}{global,local}.php',
        	'../../../config/autoload/phpunit.php',
        	__DIR__.'/config/module.config.php',
        ),
        'module_paths' => array(
            '../../../module',
            '../../../vendor',
        ),
    ),
);