<?php

namespace App\Entity\Service;

use App\Entity\Simple as SimpleEntity;
use App\Entity\EntityInterface;

class SimpleFactory implements FactoryInterface
{
    /**
     * Прототип сущности
     *
     * @var EntityInterface
     */
    protected $entityProto = null;

    /**
     *
     * @param EntityInterface $entityProto
     */
    public function __construct(EntityInterface $entityProto = null)
    {
        if (null !== $entityProto) {
            $this->setEntityProto($entityProto);
        }
    }

    /**
     * Создает новую сущность
     *
     * @param mixed $data
     * @return EntityInterface
     * @throws \Exception
     */
    public function create($data)
    {
        if ($data instanceof EntityInterface) {
            $data = $data->getValues();
        } else if (is_object($data)) {
            $data = (array) $data;
        }

        if (!is_array($data)) {
            throw new \Exception('cannot create entity');
        }

        $entity = clone $this->getEntityProto();
        $entity->exchangeArray($data);

        return $entity;
    }

    /**
     *
     * @return EntityInterface
     */
    public function getEntityProto()
    {
        if (null === $this->entityProto) {
            $this->setEntityProto(new SimpleEntity);
        }

        return $this->entityProto;
    }

    /**
     *
     * @param EntityInterface $entityProto
     * @return \App\Entity\Service\SimpleFactory
     */
    public function setEntityProto(EntityInterface $entityProto)
    {
        $this->entityProto = $entityProto;

        return $this;
    }

    /**
     * Отдает класс объекта который может создать фабрика
     *
     * @return string
     */
    public function getInstanceClass()
    {
        return get_class($this->getEntityProto());
    }
}