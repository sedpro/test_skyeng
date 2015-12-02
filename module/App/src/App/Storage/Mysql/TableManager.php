<?php

namespace App\Storage\Mysql;

class TableManager
{
    protected $tables;

    public function setTables($tables)
    {
        $this->tables = $tables;
    }

    public function get($name)
    {
        if (!array_key_exists($name, $this->tables)) {
            return $name;
        }

        return isset($this->tables[$name]['alias'])
            ? $this->tables[$name]['alias']
            : $name;
    }
}