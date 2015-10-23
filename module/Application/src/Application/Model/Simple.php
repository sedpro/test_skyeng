<?php

namespace Application\Model;

class Simple
{
    /** @var \Zend\Db\Adapter\Adapter dbAdapter */
    protected $dbAdapter;

    /** @var string table */
    protected $table;

    /** @var \Zend\Db\ResultSet\HydratingResultSet  */
    protected $hydratingResultSet;

    /** @var  \Application\Entity\EntityInterface */
    protected $entity;

    public function __construct($table, $dbAdapter, $prototype)
    {
        $this->table = $table;

        $this->dbAdapter = $dbAdapter;

        $this->entity = $prototype;

        $this->hydratingResultSet = new \Zend\Db\ResultSet\HydratingResultSet(null, $prototype);
    }

    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Execute query
     *
     * @param $sql string
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    protected function execute($sql)
    {
        return $this->dbAdapter->createStatement($sql)->execute();
    }

    /**
     * Execute query and hydrate result
     *
     * @param $sql string
     * @return \Zend\Db\ResultSet\ResultSet
     */
    protected function fetch($sql)
    {
        $result = $this->execute($sql);

        return $this->hydratingResultSet->initialize($result);
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->fetch("select /*4*/ * from {$this->table};");
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $id  = (int) $id;

        $sql = "select /*5*/ * from {$this->table} where id={$id};";

        return $this->fetch($sql)->current();
    }

    /**
     * @inheritdoc
     */
    public function insert($entity)
    {
        $fields = array_diff_key($this->entity->getFields(), ['id']);

        $values = [];
        foreach ($fields as $field) {
            $values[] = "'" . $entity->$field . "'";
        }

        $sql = "insert /*6*/ into {$this->table} (" . implode(',', $fields) . ") values (". implode(',', $values) .");";

        return $this->execute($sql)->getGeneratedValue();
    }

    /**
     * Insert a row using form values
     *
     * @param $values
     * @return mixed|null
     */
    public function insertFromForm($values)
    {
        $entity = $this->getEntity();
        foreach ($entity->getFields() as $field) {
            if (isset($values[$field])) {
                $entity->$field = $values[$field];
            }
        }

        return $this->insert($entity);
    }

    /**
     * @inheritdoc
     */
    public function delete($entity)
    {
        $fields = $this->entity->getFields();
        $where = [];
        foreach ($fields as $field) {
            if ($entity->$field!==null){
                $where[] = $field . "='" . $entity->$field . "' ";
            }
        }

        if (empty($where)) {
            return false;
        }

        $sql = "delete /*11*/ from {$this->table} where " . implode('and ', $where);
        $this->execute($sql);

        return true;
    }

    /**
     * Get maximal id
     *
     * @return int
     */
    public function getMaxId()
    {
        $sql = "select /*7*/ * from {$this->table} order by id desc limit 1;";

        $row = $this->fetch($sql)->current();
        if ($row) {
            return $row->id;
        }

        return 0;
    }

    /**
     * @inheritdoc
     */
    public function getByIds($ids, $field = 'id')
    {
        if (empty($ids)) {
            return [];
        }

        $sql = "select /*12*/ * from {$this->table} where {$field} in (" . implode(',',$ids) . ")";

        return $this->fetch($sql);
    }
}