<?php

return array(
    'modules' => array(    	
    	'BricksFile',    	
    	'BricksAsset',        
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../../config/autoload/{,*.}{global,local}.php',
        	'../../../config/autoload/phpunit.local.php',
        	'./config/module.config.php',        	
        ),
        'module_paths' => array(
            '../../../module',
            '../../../vendor',
        ),
    ),
);