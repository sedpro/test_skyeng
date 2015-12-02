<?php

namespace App\Storage;

interface StorageAwareInterface
{
    /**
     * Устанавливает объект хранилища
     *
     * @param object $storage
     */
    public function setStorage($storage);
}