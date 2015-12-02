<?php

namespace Application\Service;

class Teacher extends \App\Service\MysqlStorageble
{
    public function getMaxId()
    {
        $item = $this->getStorage()->getItem([], 1, 0, $order = 'id desc');

        return $item
            ? $item->id
            : 0;
    }
}