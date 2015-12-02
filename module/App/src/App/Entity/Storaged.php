<?php

namespace App\Entity;

use App\Storage\StorageAwareInterface;

class Storaged extends Simple implements StorageAwareInterface
{
    /**
     *
     * @var mixed
     */
    protected $storage = null;

    /**
     *
     * @param mixed $storage
     * @return \App\Entity\Storaged
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Сохраняет объект в БД
     *
     * @return \App\Entity\Storaged
     */
    public function save()
    {
        $this->getStorage()->save($this);

        return $this;
    }

    /**
     * Удаляет объект из БД
     *
     * @return \App\Entity\Storaged
     */
    public function delete()
    {
        $this->getStorage()->delete($this);

        return $this;
    }

    /**
     * Отдает объект хранилища
     *
     * @return mixed
     * @throws \Exception
     */
    public function getStorage()
    {
        if (null === $this->storage) {
            throw new \Exception('storage not set');
        }

        return $this->storage;
    }
}
