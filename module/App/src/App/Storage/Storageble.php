<?php

namespace App\Storage;

trait Storageble
{
    /**
     * @var StorageInterface
     */
    protected $storage = null;

    /**
     * @param StorageInterface $storage
     * @return Storageble
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    public function __call($name, $arguments)
    {
        if (is_callable([$this->getStorage(), $name])) {
            return call_user_func_array([$this->getStorage(), $name], $arguments);
        } else {
            throw new \Exception('Method ' . $name . ' not found');
        }
    }
}