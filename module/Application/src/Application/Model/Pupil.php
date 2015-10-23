<?php

namespace Application\Model;

class Pupil extends Simple implements MysqlModelInterface
{
    /**
     * Show common pupils of two teachers, who have largest amount of common pupils
     *
     * @param $first
     * @param $second
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getCommonPupilsByTeacherIds($first, $second)
    {
        $sql = "
            select /*10*/ * from  pupil p
            inner join teacher_pupil tp on p.id=tp.pupil_id and tp.teacher_id={$first}
            where p.id in (
                select p.id from pupil p inner join teacher_pupil tp on p.id=tp.pupil_id and tp.teacher_id={$second}
            );";

        return $this->fetch("$sql");
    }

    /**
     * Get pupils of teachers by teacher ids
     *
     * @param $ids
     * @return array|\Zend\Db\ResultSet\ResultSet
     */
    public function getPupilsByTeacherIds($ids)
    {
        if (!is_array($ids)) {
            $ids = (array) $ids;
        }

        if (empty($ids)) {
            return [];
        }

        $sql = "
          select /*1*/ p.* from {$this->table} p
          inner join teacher_pupil tp on p.id=tp.pupil_id
          where tp.teacher_id in (" . implode(',', $ids) . ");";

        return $this->fetch("$sql");
    }

    /**
     * Get pupils, with names beginning with $letters
     *
     * @param $letters
     * @param int $maxVariants
     * @param array $exceptIds
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public function getByFirstLettersOfName($letters, $maxVariants = 10, $exceptIds = [])
    {
        $letters = mb_strtolower($letters);

        $where = empty($exceptIds)
            ? ''
            : (' and id not in (' . implode(',',$exceptIds) . ')');

        $sql = "select /*8*/ id, name from {$this->table} where lcase(name) like '{$letters}%' {$where} limit {$maxVariants};";

        return $this->execute($sql);
    }
}