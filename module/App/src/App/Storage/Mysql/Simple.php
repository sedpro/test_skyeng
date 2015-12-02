<?php

namespace App\Storage\Mysql;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate;

use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

use App\Storage\StorageAwareInterface;
use App\Entity\EntityInterface;

class Simple implements \App\Storage\StorageInterface
{
    /**
     *
     * @var TableGateway
     */
    protected $tableGateway = null;

    /**
     * Список допустимых к изменению полей таблицы
     *
     * @var array
     */
    protected $fields = array();

    /**
     * @var TableManager
     */
    protected $tableManager;

    /**
     *
     * @param \Zend\Db\TableGateway\TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway, array $fields = array())
    {
        $this->tableGateway = $tableGateway;

        $itemSetProto = $this->tableGateway->getResultSetPrototype();
        if (!$itemSetProto instanceof EntitySetInterface) {
            throw new \Exception('ResultSetPrototype must implement TableEntitySetInterface');
        }

        if ($itemSetProto instanceof StorageAwareInterface) {
            $itemSetProto->setStorage($this);
        }

        if (!empty($fields)) {
            $this->setFields($fields);
        }
    }

    /**
     *
     * @return TableEntitySetInterface
     */
    public function getItemSetProto()
    {
        return $this->tableGateway->getResultSetPrototype();
    }

    /**
     * Делает клон прототипа набора данных и инициализирует его по необходимости
     *
     * @param mixed $data
     * @return TableEntitySetInterface
     */
    public function createItemSet($data = null)
    {
        $itemSet = clone $this->getItemSetProto();
        if (null !== $data) {
            $itemSet->initialize($data);
        }

        return $itemSet;
    }

    /**
     *
     * @param array $fields
     * @return \App\Storage\Mysql\Simple
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param $tableManager TableManager
     * @return $this
     */
    public function setTableManager(TableManager $tableManager)
    {
        $this->tableManager = $tableManager;

        return $this;
    }

    /**
     * @return TableManager
     */
    public function getTableManager()
    {
        return $this->tableManager;
    }

    /**
     * Отдает объект по ключу
     *
     * @param string|integer $id
     * @return mixed
     * @throws \Exception
     */
    public function getByKey($id)
    {
        $rowset = $this->tableGateway->select(
            array(
                $this->getItemSetProto()->getFactory()->getEntityProto()->getKeyName() => $id
            )
        );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    /**
     * Отдает набор объектов согласно заданому диапазону ключей
     *
     * @param integer $start
     * @param integer $stop
     * @return TableEntitySetInterface
     */
    public function getRangeByKey($start, $stop)
    {
        $sql = new Sql($this->getAdapter());
        $select = $sql->select();

        $select->from($this->tableGateway->getTable());
        $select->where(
            new Predicate\Between(
                $this->getItemSetProto()->getFactory()->getEntityProto()->getKeyName(),
                $start,
                $stop
            )
        );

        return $this->tableGateway->selectWith($select);
    }

    /**
     * Сохраняет объект в таблице
     *
     * @param EntityInterface $object
     * @throws \Exception
     */
    public function save(EntityInterface $object)
    {
        $data = array();
        foreach ($this->fields as $field) {
            $data[$field] = $object->{$field};
        }

        $id = $object->getKeyValue();
        if (null === $id) {
            $this->tableGateway->insert($data);
            $object->{$object->getKeyName()} = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getByKey($id, $object->getKeyName())) {
                $this->tableGateway->update($data, array($object->getKeyName() => $id));
            } else {
                throw new \Exception('row with ' . $id . ' id does not exist');
            }
        }
    }

    /**
     * Удаляет объект
     *
     * @param mixed $id
     * @return \App\Storage\Mysql\Simple
     */
    public function delete($id)
    {
        if ($id instanceof EntityInterface) {
            $id = $id->getKeyValue();
        }

        $this->tableGateway->delete(
            array(
                $this->getItemSetProto()->getFactory()->getEntityProto()->getKeyName() => $id
            )
        );

        return $this;
    }

    /**
     *
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->tableGateway->getAdapter();
    }

    /**
     * @param Select $select
     * @return AdapterInterface
     */
    protected function _getPaginatorAdapter($select = null)
    {
        $sql = new Sql($this->getAdapter());
        if ($select === null) {
            $select = $sql->select($this->tableGateway->getTable());
        }
        return new DbSelect($select, $sql, $this->getItemSetProto());
    }

    /**
     * @return AdapterInterface
     */
    public function getPaginatorAdapter()
    {
        return $this->_getPaginatorAdapter();
    }

    protected function getTable()
    {
        return $this->tableGateway->getTable();
    }

    /**
     * @param \Zend\Db\Sql\Select $select
     * @return mixed
     */
    protected function getSql($select)
    {
        return $select->getSqlString($this->tableGateway->getAdapter()->getPlatform());
    }

    protected function getSelect()
    {
        return (new Sql($this->getAdapter()))->select();
    }

    protected function selectWith($select)
    {
        return $this->tableGateway->selectWith($select);
    }

    /**
     * @param array $where ['equalTo' => ['type', 'single'], 'in' => ['id', [1, 2]], ]
     * @param null $limit
     * @param int $offset
     * @param string $order
     * @return null|\Zend\Db\ResultSet\ResultSetInterface
     */
    public function getItemsWhere($where = [], $limit = null, $offset = 0, $order = 'id asc')
    {
        $select = $this->getSelect()
            ->from($this->getTable());

        if ($where) {
            $whereObj = new \Zend\Db\Sql\Where();
            foreach ($where as $key => $params) {
                if (in_array($key, $this->fields)) {
                    if(is_array($params)) {
                        if ($params) {
                            $whereObj->in($key, $params);
                        } else {
                            // в случае когда передано $service->getItemsWhere(['id => []]);
                            return [];
                        }
                    } else {
                        $whereObj->equalTo($key, $params);
                    }
                } else {
                    $allowedMethods = [
                        'equalTo',
                        'notEqualTo',
                        'lessThan',
                        'greaterThan',
                        'lessThanOrEqualTo',
                        'greaterThanOrEqualTo',
                        'like',
                        'notLike',
                        'expression',
                        'literal',
                        'isNull',
                        'isNotNull',
                        'in',
                        'notIn',
                        'between',
                    ];
                    if (!in_array($key, $allowedMethods)) {
                        throw new \Exception("method '" . $key . "' not allowed in where.");
                    }
                    call_user_func_array([$whereObj, $key], $params);
                }
            }

            $select->where($whereObj);
        }

        if ($limit !== null) {
            $select->limit($limit);
        }

        if ($offset) {
            $select->offset($offset);
        }

        $select->order($order);

        return $this->tableGateway->selectWith($select);
    }

    public function getItem($where = [], $limit = null, $offset = 0, $order = 'id asc')
    {
        return $this->getItemsWhere($where, $limit, $offset, $order)->current();
    }

    public function update($set, $where)
    {
        if (!$where) {
            return [];
        }

        return $this->tableGateway->update($set, $where);
    }

    public function insert($set)
    {
        $set = array_intersect_key($set, array_flip($this->fields));

        return $this->tableGateway->insert($set)
            ? $this->tableGateway->lastInsertValue
            : null;
    }

    /**
     * @param $parameters
     * @return mixed
     * @throws \Exception
     */
    public function bulkInsert($parameters)
    {
        $params = [];
        $values = [];
        foreach($parameters as $firstLevel) {
            foreach($firstLevel as $secondLevel) {
                $params[] = $secondLevel;
            }
            $values[] = '(' . implode(',', array_fill(1, count($firstLevel), '?')) . ')';
        }
        $values = implode(',', $values);

        $firstLevel = array_shift($parameters);
        if (!empty(array_diff(array_keys($firstLevel), $this->fields))) {
            throw new \Exception('wrong field found while bulk insert');
        }
        $fields = '`' . implode('`,`', array_keys($firstLevel)) . '`';

        $sql = "INSERT INTO `{$this->getTable()}` ({$fields}) VALUES {$values};";

        $resultSet = $this->getAdapter()->query($sql, $params);

        return $resultSet->getAffectedRows();
    }

    public function executeRawSql($sql)
    {
        return $this->tableGateway
            ->getAdapter()
            ->createStatement($sql)
            ->execute();
    }

    public function fetchRawSql($sql)
    {
        return $this->tableGateway
            ->getResultSetPrototype()
            ->set(
                $this->executeRawSql($sql)
            );
    }

    protected function selectFoundRows()
    {
        return (int) $this->tableGateway
            ->getAdapter()
            ->createStatement('SELECT FOUND_ROWS()')
            ->execute()
            ->current()['FOUND_ROWS()'];
    }
}