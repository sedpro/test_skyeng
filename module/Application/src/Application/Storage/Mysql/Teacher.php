<?php

namespace Application\Storage\Mysql;

use Zend\Db\Sql;
use Zend\Db\Sql\Expression;

class Teacher extends \App\Storage\Mysql\Simple
{
    static public $alias = 'teacher';

    /**
     *
     * @var array
     */
    protected $fields = [
        'id',
        'name',
        'gender',
        'phone',
    ];

    public function getItemsForPaginator($page, $itemsPerPage)
    {
        $teacherPupilTableName = $this->getTableManager()->get(\Application\Storage\Mysql\TeacherPupil::$alias);

        $select = $this->getSelect()
            ->from(['t' => $this->getTable()])
            ->columns(['*', 'pupils' => new Expression("count('tp.id')")])
            ->quantifier('DISTINCT SQL_CALC_FOUND_ROWS')
            ->join(['tp' => $teacherPupilTableName], "t.id=tp.teacher_id", [], Sql\Select::JOIN_LEFT)
            ->group('t.id')
            ->order('t.id asc')
            ->limit($itemsPerPage)
            ->offset(($page-1)*$itemsPerPage);

        $items = $this->tableGateway->selectWith($select);

        $items = iterator_to_array($items);

        $pupils = [];
        foreach ($items as $item) {
            $pupils[$item->id] = $item->pupils;
        }

        return [
            'items' => $items,
            'count' => $this->selectFoundRows(),
            'pupils' => $pupils,
        ];
    }

    public function getTeachersOfPupilsBornInMonth($month, $page, $itemsPerPage)
    {
        $pupilTableName = $this->getTableManager()->get(\Application\Storage\Mysql\Pupil::$alias);
        $teacherTableName = $this->getTableManager()->get(\Application\Storage\Mysql\Teacher::$alias);
        $teacherPupilTableName = $this->getTableManager()->get(\Application\Storage\Mysql\TeacherPupil::$alias);

        $sql = "
            select /*1*/ SQL_CALC_FOUND_ROWS t.* from {$teacherTableName} t where id in (
              select id from (
                select a.* from (
                    select t.id, month(p.birthday) as birth_month
                    from {$pupilTableName} p
                    inner join {$teacherPupilTableName} tp on p.id=tp.pupil_id
                    inner join {$teacherTableName} t on t.id=tp.teacher_id
                    group by t.id, birth_month
                ) as a
                group by id
                having count(*)=1 and birth_month={$month}
              ) as b
            )
            order by id asc
            limit {$itemsPerPage}
            offset " . ($page-1)*$itemsPerPage;

        $items = $this->fetchRawSql($sql);

        return [
            'items' => $items,
            'count' => $this->selectFoundRows(),
        ];
    }
}