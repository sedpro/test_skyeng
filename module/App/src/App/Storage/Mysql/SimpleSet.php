<?php

namespace App\Storage\Mysql;

use App\Entity\SimpleSet as EntitySimpleSet;

class SimpleSet extends EntitySimpleSet implements EntitySetInterface
{
    /**
     *
     * @var integer
     */
    protected $fieldCount = null;

    /**
     *
     * @param mixed $dataSource
     */
    public function initialize($dataSource)
    {
        $this->set($dataSource);
    }

    /**
     *
     * @return integer
     * @throws \Exception
     */
    public function getFieldCount()
    {
        if (null !== $this->fieldCount) {
            return $this->fieldCount;
        }

        if (!count($this)) {
            return 0;
        }

        $this->rewind();
        if (!$this->valid()) {
            $this->fieldCount = 0;

            return 0;
        }

        $row = $this->current();
        if (is_object($row) && $row instanceof Countable) {
            $this->fieldCount = $row->count();
        } else {
            $row = (array) $row;
            $this->fieldCount = count($row);
        }

        return $this->fieldCount;
    }
}