<?php

namespace Application\Entity;

abstract class Simple
{
    protected $fields = [];

    /** @var  int Primary field - this field we have in all tables */
    public $id;

    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Needed for hydrating
     *
     * @param $data
     */
    public function exchangeArray($data)
    {
        foreach ($this->fields as $field) {
            $this->$field = empty($data[$field])
                ? null
                : $data[$field];
        }
    }

    public function getId()
    {
        return $this->id;
    }
}