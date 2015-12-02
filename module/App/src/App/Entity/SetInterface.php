<?php

namespace App\Entity;

use App\Entity\Service\FactoryInterface;

interface SetInterface extends \Iterator, \Countable, \ArrayAccess
{
    /**
     * Установить данные
     *
     * @param mixed $set
     */
    public function set($set);

    /**
     * Добавить данные
     *
     * @param mixed $data
     */
    public function add($data);

    /**
     * Возвратить данные в виде массива
     *
     * @return array
     */
    public function toArray();

    /**
     * Сбросить данные
     */
    public function reset();

    /**
     * Установить фабрику для создания сущностей
     *
     * @param FactoryInterface $factory
     */
    public function setFactory(FactoryInterface $factory);

    /**
     * @return FactoryInterface
     */
    public function getFactory();
}