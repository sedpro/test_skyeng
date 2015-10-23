<?php

namespace Application\Model;

use Zend\ServiceManager\ServiceLocatorInterface;

class Factory
{
    /**
     * Create instance of \Application\Model\MysqlModelInterface
     *
     * NB! This class doesn't implement \Zend\ServiceManager\FactoryInterface thus in service_manager
     * it should be set up in 'invokables', not in 'factories'
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $table
     * @return MysqlModelInterface
     */
    public function create(ServiceLocatorInterface $serviceLocator, $name, $table)
    {
        $adapter = $serviceLocator->get('DbAdapter');

        $entityName = '\\Application\\Entity\\' . $name;
        $className = '\\Application\\Model\\' . $name;

        $entity = new $entityName;
        $table = new $className($table, $adapter, $entity);

        return $table;
    }
}