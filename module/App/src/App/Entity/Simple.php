<?php

namespace App\Entity;

class Simple implements EntityInterface
{
    /**
     *
     * @var string 
     */
    protected $keyName = 'id';

    /**
     *
     * @var array
     */
    protected $data = array();

    /**
     *
     * @var \Zend\Filter\Word\UnderscoreToCamelCase
     */
    protected static $nameFilter = null;

    /**
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (!empty($data)) {
            $this->exchangeArray($data);
        }
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    public function exchangeArray(array $data)
    {
        $old = $this->getValues();
        foreach ($data as $property => $value) {
            $this->{$property} = $value;
        }

        return $old;
    }

    /**
     * Преобразует имя свойства к внутреннему формату
     *
     * @param string $name
     * @return string
     */
    protected function filterName($name)
    {
        if (false === strpos($name, '_')) {
            return $name;
        } else {
            if (null === static::$nameFilter) {
                static::$nameFilter = new \Zend\Filter\Word\UnderscoreToCamelCase();
            }

            return lcfirst(static::$nameFilter->filter($name));
        }
    }

    /**
     *
     * @return array
     */
    public function getValues()
    {
        $values = array();
        foreach ($this->data as $key => $value) {
            if ($value instanceof EntityInterface) {
                $values[$key] = $value->getValues();
            } else if ($value instanceof SetInterface) {
                $values[$key] = $value->toArray();
            } else {
                $values[$key] = $value;
            }
        }

        return $values;
    }

    /**
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     *
     * @return mixed
     */
    public function getKeyValue()
    {
        return $this->{$this->getKeyName()};
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     * @return \App\Entity\Simple
     */
    public function __set($name, $value)
    {
        $validName = $this->filterName($name);
        $method = 'set' . ucfirst($validName);
        if (method_exists($this, $method)) {
            call_user_func(array($this, $method), $value);
        } else {
            $this->data[$validName] = $value;
        }

        return $this;
    }

    /**
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $validName = $this->filterName($name);
        $method = 'get' . ucfirst($validName);
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        } elseif (array_key_exists($validName, $this->data)) {
            return $this->data[$validName];
        }

        return null;
    }

    /**
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        $validName = $this->filterName($name);
        return isset($this->data[$validName]);
    }

    /**
     *
     * @param string $name
     */
    public function __unset($name)
    {
        $validName = $this->filterName($name);
        if (isset($this->$validName)) {
            $this->data[$validName] = null;
        }
    }
}