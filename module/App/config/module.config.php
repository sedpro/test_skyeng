<?php

namespace App;

return array(
    'service_manager' => [
        'abstract_factories' => [
            \Zend\Log\LoggerAbstractServiceFactory::class,
            \App\Storage\Mysql\AbstractFactory::class,
        ],
        'factories' => array(
            'DbAdapter' => \Zend\Db\Adapter\AdapterServiceFactory::class,
            'tableManager' => \App\Storage\Mysql\TableManagerFactory::class,
        ),
    ],
    'view_helpers' => [
        'invokables' => [
            'showMessages' => \App\View\Helper\ShowMessages::class,
        ],
    ],
);
