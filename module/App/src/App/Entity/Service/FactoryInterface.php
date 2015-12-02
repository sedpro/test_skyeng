<?php

namespace App\Entity\Service;

use App\Entity\EntityInterface;

interface FactoryInterface
{
    /**
     * Создает новую сущность
     *
     * @param mixed $data
     * @return \App\Entity\EntityInterface
     */
    public function create($data);

    /**
     * Отдает прототип сущьности
     *
     */
    public function getEntityProto();

    /**
     * Устанавливает объект прототипа сущьности
     *
     * @param \App\Entity\EntityInterface $entityProto
     */
    public function setEntityProto(EntityInterface $entityProto);

    /**
     * Отдает класс объекта который может создать фабрика
     *
     * @return string
     */
    public function getInstanceClass();
}