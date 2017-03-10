<?php

/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * The MIT License (MIT)
 * Copyright (c) 2015 bricks-cms.org
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

use Zend\Router\Http\Segment;
use Bricks\Asset\Controller;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class
        ],
    ],
    'router' => [
        'routes' => [
            'backend' => [
                'child_routes' => [
                    'system' => [
                        'child_routes' => [
                            'assets' => [
                                'type' => Segment::class,
                                'may_terminate' => true,
                                'options' => [
                                    'route'    => '/{assets}',
                                    'defaults' => [
                                        'controller' => Controller\IndexController::class,
                                        'action'     => 'publish',
                                    ],
                                ],
                                'child_routes' => [
                                    'publish' => [
                                        'type' => Segment::class,
                                        'may_terminate' => true,
                                        'options' => [
                                            'route'    => '/{publish}',
                                            'defaults' => [
                                                'controller' => Controller\IndexController::class,
                                                'action'     => 'publish',
                                            ],
                                        ],
                                    ],
                                ]
                            ],
                        ]
                    ]
                ]
            ]
        ],
    ],
    'navigation' => [
        'backend-navigation' => [
            'backend' => [
                'pages' => [
                    'backend/system' => [
                        'pages' => [
                            'assets' => [
                                'label' => 'Assets',
                                'route' => 'backend/system/assets',
                                'controller' => Controller\IndexController::class,
                                'action' => 'publish',
                                'pages' => [
                                    'publish' => [
                                        'label' => 'Publish',
                                        'route' => 'backend/system/assets/publish',
                                        'controller' => Controller\IndexController::class,
                                        'action' => 'publish',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'phparray',
                'base_dir' => __DIR__.'/../lang',
                'pattern' => '%s/routes.php',
                'text_domain' => 'routes'
            ],
            [
                'type' => 'phparray',
                'base_dir' => __DIR__.'/../lang',
                'pattern' => '%s/lang.php',
            ]
        ]
    ]
];