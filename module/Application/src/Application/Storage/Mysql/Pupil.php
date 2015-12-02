<?php

namespace Application\Storage\Mysql;

use Zend\Db\Sql;
use Zend\Db\Sql\Expression;

class Pupil extends \App\Storage\Mysql\Simple
{
    static public $alias = 'pupil';

    /**
     *
     * @var array
     */
    protected $fields = [
        'id',
        'name',
        'email',
        'birthday',
        'level',
    ];

    public function getByTeacherIds($teacher_ids)
    {
        $teacherPupilTableName = $this->getTableManager()->get(\Application\Storage\Mysql\TeacherPupil::$alias);

        $select = $this->getSelect()
            ->from(['p' => $this->getTable()])
            ->join(['tp' => $teacherPupilTableName], "p.id=tp.pupil_id", []);

        $select->where(['tp.teacher_id' => $teacher_ids]);

        return $this->tableGateway->selectWith($select);
    }

    public function getByFirstLettersOfName($letters, $limit = 10, $exclude = [])
    {
        $select = $this->getSelect()
            ->columns(['id', 'name'])
            ->from($this->getTable())
            ->limit($limit);

        if ($exclude) {
            $select->where->notIn('id', $exclude);
        }

        $select->where->like(new Expression("lcase(`name`)"), mb_strtolower($letters) . '%');

        return $this->tableGateway->selectWith($select);
    }

    /**
     * Show common pupils of two teachers, who have largest amount of common pupils
     *
     * @param $first
     * @param $second
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getCommonPupilsByTeacherIds($first, $second)
    {
        $teacherPupilTableName = $this->getTableManager()->get(\Application\Storage\Mysql\TeacherPupil::$alias);

        $subSelect = $this->getSelect()
            ->columns(['id'])
            ->from(['p' => $this->getTable()])
            ->join(['tp' => $teacherPupilTableName], "p.id=tp.pupil_id", []);

        $subSelect->where->equalTo('tp.teacher_id', $second);

        $select = $this->getSelect()
            ->from(['p' => $this->getTable()])
            ->join(['tp' => $teacherPupilTableName], "p.id=tp.pupil_id", []);

        $select->where
            ->in('p.id', $subSelect)
            ->equalTo('tp.teacher_id', $first);

        return $this->tableGateway->selectWith($select);
    }
}