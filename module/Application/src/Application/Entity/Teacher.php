<?php

namespace Application\Entity;

class Teacher extends Simple implements EntityInterface
{
    protected $fields = [
        'id',
        'name',
        'gender',
        'phone',
    ];

    const GENDER_MALE = 'm';
    const GENDER_FEMALE = 'f';

    public $genders = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];

    public static $gender_text = [
        self::GENDER_MALE => 'male',
        self::GENDER_FEMALE => 'female',
    ];

    public $id;
    public $name;
    public $gender;
    public $phone;

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    public function getGender()
    {
        return self::$gender_text[$this->gender];
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }
}
