<?php

namespace App\Storage\Mysql;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TableManagerFactory implements FactoryInterface
{
    /**
    * Create TableManager service
    *
    * @param ServiceLocatorInterface $serviceLocator
    * @return TableManager
    */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $tableManager = new TableManager();
        $tableManager->setTables($config['storaged']);

        return $tableManager;
    }
}
