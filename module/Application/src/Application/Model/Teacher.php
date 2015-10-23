<?php

namespace Application\Model;

class Teacher extends Simple implements MysqlModelInterface
{
    /**
     * Get data for paginator
     *
     * @param $page
     * @param $itemsPerPage
     * @return array
     */
    public function getItemsForPaginator($page, $itemsPerPage)
    {
        $sql = "
            select /*2*/ SQL_CALC_FOUND_ROWS t.*, count(tp.id) as pupils
            from {$this->table} t
            left join teacher_pupil tp on t.id=tp.teacher_id
            group by t.id
            order by id asc
            limit $itemsPerPage
            offset " . ($page-1)*$itemsPerPage;

        $result = $this->execute($sql);
        $result = iterator_to_array($result);

        $pupils = [];
        foreach ($result as $item) {
            $pupils[$item['id']] = $item['pupils'];
        }

        $items = $this->hydratingResultSet->initialize($result);

        $sql = "SELECT FOUND_ROWS();";
        $count = (int) $this->execute($sql)->current()['FOUND_ROWS()'];

        return [
            'items' => $items,
            'count' => $count,
            'pupils' => $pupils,
        ];
    }

    /**
     * Get teachers who learn only pupils, born in certain month.
     *
     * Is adapted for paginator.
     *
     * @param $month
     * @param $page
     * @param $itemsPerPage
     * @return array
     */
    public function getTeachersOfPupilsBornInMonth($month, $page, $itemsPerPage)
    {
        $sql = "
            select /*3*/ SQL_CALC_FOUND_ROWS t.* from teacher t where id in (
                select id from (select a.* from (
                    select t.id, month(p.birthday) as birth_month from pupil p
                    inner join teacher_pupil tp on p.id=tp.pupil_id
                    inner join teacher t on t.id=tp.teacher_id
                    group by t.id, birth_month
                ) as a
                group by id
                having count(*)=1 and birth_month={$month}
            ) as b )
            order by id asc
            limit $itemsPerPage
            offset " . ($page-1)*$itemsPerPage;

        $items = $this->fetch($sql);

        $sql = "SELECT FOUND_ROWS();";
        $count = (int) $this->execute($sql)->current()['FOUND_ROWS()'];

        return [
            'items' => $items,
            'count' => $count,
        ];
    }

    /**
     * Get two teachers, who have largest amount of common pupils
     *
     * @return array|mixed
     */
    public function getTeachersWithMostCommonPupils()
    {
        $sql = "
          select /*9*/ a.teacher_id as first, b.teacher_id as second
          from teacher_pupil a
          inner join teacher_pupil b on a.pupil_id=b.pupil_id and a.teacher_id>b.teacher_id
          group by a.teacher_id, b.teacher_id
          order by count(*) desc
          limit 1;
        ";

        $result = $this->execute($sql);

        if (!$result) {
            return [];
        }

        return $result->current();
    }
}