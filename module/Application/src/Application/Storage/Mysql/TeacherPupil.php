<?php

namespace Application\Storage\Mysql;

use Zend\Db\Sql;
use Zend\Db\Sql\Expression;

class TeacherPupil extends \App\Storage\Mysql\Simple
{
    static public $alias = 'teacher_pupil';

    /**
     *
     * @var array
     */
    protected $fields = [
        'id',
        'teacher_id',
        'pupil_id',
    ];


    /**
     * Get two teachers, who have largest amount of common pupils
     *
     * @return array|mixed
     */
    public function getTeachersWithMostCommonPupils()
    {
        $select = $this->getSelect()
            ->from(['a' => $this->getTable()])
            ->columns([
                'first' => 'teacher_id'
            ])
            ->join(['b' => $this->getTable()], "a.pupil_id=b.pupil_id and a.teacher_id>b.teacher_id", [ 'second' => 'teacher_id'])
            ->group(['a.teacher_id', 'b.teacher_id'])
            ->order(new Expression("count(*) desc"))
            ->limit(1);

        return $this->tableGateway->selectWith($select)->current();
    }
}