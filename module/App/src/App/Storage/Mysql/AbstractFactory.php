<?php

namespace App\Storage\Mysql;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractFactory implements AbstractFactoryInterface
{
    const DEFAULT_STORAGE_ADAPTER = '\\Application\\Storage\\Mysql\\';

    const SERVICE_FOLDER = '\\Application\\Service\\';

    const ENTITY_FOLDER = '\\Application\\Entity\\';

    const DEFAULT_SERVICE = \App\Service\MysqlStorageble::class;

    const DEFAULT_ENTITY = \App\Entity\Storaged::class;

    /** @var array */
    protected $config;

    /**
     *
     * @var \Zend\Filter\Word\UnderscoreToCamelCase
     */
    protected static $nameFilter = null;

    protected static $reverseFilter = null;

    /**
     * Can we create a service by the requested name?
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $name = $this->filterName($requestedName);

        $config = $this->getConfig($serviceLocator);

        if (!array_key_exists($name, $config) && ! in_array($name, $config)) {
            return false;
        }

        return class_exists($this->getStorage($name, $config));
    }

    /**
     * get array of methods to cache for given service
     *
     * @param ServiceLocatorInterface $services
     * @return array
     * @throws \Exception No config entry found
     */
    protected function getConfig(ServiceLocatorInterface $services)
    {
        if ($this->config !== null) {
            return $this->config;
        }
        $config = $services->get('Config');
        if (!isset($config['storaged'])) {
            throw new \Exception('config not configured for storaged abstract table');
        }
        $this->config = $config['storaged'];
        return $this->config;
    }

    /**
     * Create a service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  string $name
     * @param  string $requestedName
     * @return \Zend\Db\Adapter\Adapter
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $name = $this->filterName($requestedName);
        $config = $this->getConfig($serviceLocator);
        $storageName = $this->getStorage($name, $config);
        $serviceName = $this->getService($name, $config);
        $entityName = $this->getEntity($name, $config);

        $entity = new $entityName;

        $factory = new \App\Entity\Service\SimpleFactory($entity);
        $set = new \App\Storage\Mysql\SimpleSet(null, $factory);

        $tableManager = $serviceLocator->get('TableManager');

        $tableGateway = new \Zend\Db\TableGateway\TableGateway(
            $tableManager->get($name),
            $serviceLocator->get('DbAdapter'),
            null,
            $set
        );

        $storage = new $storageName($tableGateway);
        $storage->setTableManager($tableManager);

        $service = new $serviceName;
        $service->setStorage($storage);

        return $service;
    }

    /**
     * @param $name
     * @param $config
     * @return array
     */
    private function getStorage($name, $config)
    {
        if (isset($config[$name]['storage'])) {
            return $config[$name]['storage'];
        }

        return self::DEFAULT_STORAGE_ADAPTER . $this->reverseFilter($name);
    }

    /**
     * @param $name
     * @param $config
     * @return array
     */
    private function getService($name, $config)
    {
        if (isset($config[$name]['service'])) {
            return $config[$name]['service'];
        }

        if (class_exists($class = self::SERVICE_FOLDER . $name)) {
            return $class;
        }

        return self::DEFAULT_SERVICE;
    }

    private function getEntity($name, $config)
    {
        if (isset($config[$name]['entity'])) {
            return $config[$name]['entity'];
        }

        if (class_exists($class = self::ENTITY_FOLDER . $name)) {
            return $class;
        }

        return self::DEFAULT_ENTITY;
    }

    /**
     * Преобразует имя свойства к внутреннему формату
     *
     * @param string $name
     * @return string
     */
    protected function filterName($name)
    {
        $name = preg_replace('/Service$/', '', $name);

        if (null === static::$nameFilter) {
            static::$nameFilter = new \Zend\Filter\Word\CamelCaseToUnderscore();
        }

        return strtolower(static::$nameFilter->filter($name));
    }

    /**
     * Преобразует имя свойства к внутреннему формату
     *
     * @param string $name
     * @return string
     */
    protected function reverseFilter($name)
    {
        if (null === static::$reverseFilter) {
            static::$reverseFilter = new \Zend\Filter\Word\UnderscoreToCamelCase();
        }

        return ucfirst(static::$reverseFilter->filter($name));
    }

}