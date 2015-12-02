<?php

namespace App\Service;

class MysqlStorageble
{
    use \App\Storage\Storageble;

    public function getItemsWhere($where = [], $limit = null, $offset = 0, $order = 'id asc') {
        return $this->storage->getItemsWhere($where, $limit, $offset, $order);
    }

    public function getItem($where = [], $limit = null, $offset = 0, $order = 'id asc') {
        return $this->storage->getItem($where, $limit, $offset, $order);
    }

    public function update($set, $where)
    {
        return $this->storage->update($set, $where);
    }

    public function insert($set)
    {
        return $this->storage->insert($set);
    }

    public function delete($where)
    {
        return $this->storage->delete($where);
    }
}