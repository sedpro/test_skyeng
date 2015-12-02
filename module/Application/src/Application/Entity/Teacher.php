<?php

namespace Application\Entity;

class Teacher extends \App\Entity\Storaged
{
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

    public function getGender()
    {
        return self::$gender_text[$this->getValues()['gender']];
    }
}