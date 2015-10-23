<?php

namespace Application\Model;

interface MysqlModelInterface
{
    /**
     * Get one item
     *
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function get($id);

    /**
     * Get all items
     *
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getAll();

    /**
     * Insert a row
     *
     * @param $entity
     * @return mixed|null
     */
    public function insert($entity);

    /**
     * @param $entity
     * @return bool
     */
    public function delete($entity);

    /**
     * @param $ids
     * @param string $field
     * @return array|\Zend\Db\ResultSet\ResultSet
     */
    public function getByIds($ids, $field = 'id');
}