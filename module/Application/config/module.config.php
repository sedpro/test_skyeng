<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => \Zend\Mvc\Router\Http\Literal::class,
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'teachers' => array(
                'type' => \Zend\Mvc\Router\Http\Segment::class,
                'options' => array(
                    'route'    => '/teacher[/:page]',
                    'constraints' => [
                        'page'     => '[0-9]+',
                    ],
                    'defaults' => array(
                        'controller' => 'Application\Controller\Teacher',
                        'action'     => 'index',
                        'page' => 1,
                        'items_per_page' => 20,
                    ),
                ),
            ),
            'teacher_month' => array(
                'type' => \Zend\Mvc\Router\Http\Segment::class,
                'options' => array(
                    'route'    => '/teacher/month/:month[/:page]',
                    'constraints' => [
                        'page'     => '[0-9]+',
                        'month'    => '[0-9]+',
                    ],
                    'defaults' => array(
                        'controller' => 'Application\Controller\Teacher',
                        'action'     => 'month',
                        'month' => 4,
                        'page' => 1,
                        'items_per_page' => 20,
                    ),
                ),
            ),
            'teacher_max' => array(
                'type' => \Zend\Mvc\Router\Http\Literal::class,
                'options' => array(
                    'route'    => '/teacher/max/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Teacher',
                        'action'     => 'max',
                    ),
                ),
            ),
            'teacher_assign' => [
                'type' => \Zend\Mvc\Router\Http\Segment::class,
                'options' => [
                    'route'    => '/teacher/assign/:id',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Application\Controller\Teacher',
                        'action'     => 'assign',
                    ],
                ],
            ],
            'teacher_new' => array(
                'type' => \Zend\Mvc\Router\Http\Literal::class,
                'options' => array(
                    'route'    => '/teacher/new',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Teacher',
                        'action'     => 'new',
                    ),
                ),
            ),
            'pupil_new' => array(
                'type' => \Zend\Mvc\Router\Http\Literal::class,
                'options' => array(
                    'route'    => '/pupil/new',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Pupil',
                        'action'     => 'new',
                    ),
                ),
            ),
            'ajax_link' => [
                'type' => \Zend\Mvc\Router\Http\Literal::class,
                'options' => [
                    'route' => '/ajax/link',
                    'defaults' => [
                        'controller' => 'Application\Controller\Teacher',
                        'action'     => 'ajaxLink',
                    ],
                ],
            ],
            'ajax_unlink' => [
                'type' => \Zend\Mvc\Router\Http\Literal::class,
                'options' => [
                    'route' => '/ajax/unlink',
                    'defaults' => [
                        'controller' => 'Application\Controller\Teacher',
                        'action'     => 'ajaxUnlink',
                    ],
                ],
            ],
            'ajax_autocomplete' => [
                'type' => \Zend\Mvc\Router\Http\Segment::class,
                'options' => [
                    'route' => '/ajax/autocomplete/:teacher_id',
                    'defaults' => [
                        'controller' => 'Application\Controller\Pupil',
                        'action'     => 'ajaxAutocomplete',
                    ],
                ],
            ],
        ),
    ),
    'service_manager' => [
        'abstract_factories' => [
            \Zend\Log\LoggerAbstractServiceFactory::class,
        ],
        'factories' => array(
            'DbAdapter' => \Zend\Db\Adapter\AdapterServiceFactory::class,
            'PupilForm' => \Application\Form\PupilFormFactory::class,
            'TeacherForm' => \Application\Form\TeacherFormFactory::class,
            'navigation' => \Zend\Navigation\Service\DefaultNavigationFactory::class,

        ),
        'invokables' => [
            'tableFactory' => \Application\Model\Factory::class,
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => Controller\IndexController::class,
            'Application\Controller\Cli' => Controller\CliController::class,
            'Application\Controller\Pupil' => Controller\PupilController::class,
            'Application\Controller\Teacher' => Controller\TeacherController::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'showMessages' => \Application\View\Helper\ShowMessages::class,
        ],
    ],
    'navigation' => [
        'default' => [
            'Home' => [
                'label' => 'Home',
                'route' => 'home',
            ],
            'Teacher' => [
                'label' => 'Teachers',
                'route' => 'teachers',
            ],
            'Add Teacher' => [
                'label' => 'Add teacher',
                'route' => 'teacher_new',
            ],
            'Add Pupil' => [
                'label' => 'Add pupil',
                'route' => 'pupil_new',
            ],
            'Teachers with max common pupils' => [
                'label' => 'Teachers with max common pupils',
                'route' => 'teacher_max',
            ],
            'Teacher Month 4' => [
                'label' => 'Teacher with only pupils born in april',
                'route' => 'teacher_month',
                'month' => 4,
            ],
        ]
    ],
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                'install' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'install [--pupils=] [--teachers=]',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Cli',
                            'action' => 'install',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
