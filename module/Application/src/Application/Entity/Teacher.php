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

    public function randomize()
    {
        $this->name = \Application\Helper\Randomizer::getRandomName();
        $this->gender = $this->genders[mt_rand(0, 1)];
        $this->phone = \Application\Helper\Randomizer::getRandomPhone();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getGender()
    {
        return self::$gender_text[$this->gender];
    }

    public function getPhone()
    {
        return $this->phone;
    }
}
