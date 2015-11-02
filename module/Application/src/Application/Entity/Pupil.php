<?php

namespace Application\Entity;

class Pupil extends Simple implements EntityInterface
{
    protected $fields = [
        'id',
        'name',
        'email',
        'birthday',
        'level',
    ];

    const LEVEL_A1 = 'a1';
    const LEVEL_A2 = 'a2';
    const LEVEL_B1 = 'b1';
    const LEVEL_B2 = 'b2';
    const LEVEL_C1 = 'c1';
    const LEVEL_C2 = 'c2';

    public static $levels = [
        self::LEVEL_A1,
        self::LEVEL_A2,
        self::LEVEL_B1,
        self::LEVEL_B2,
        self::LEVEL_C1,
        self::LEVEL_C2,
    ];

    public static $level_text = [
        self::LEVEL_A1 => 'beginner',
        self::LEVEL_A2 => 'elementary',
        self::LEVEL_B1 => 'intermediate',
        self::LEVEL_B2 => 'upper intermediate',
        self::LEVEL_C1 => 'advanced',
        self::LEVEL_C2 => 'proficiency',
    ];

    public $name;
    public $email;
    public $birthday;
    public $level;

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
        return $this;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    public function getLevel()
    {
        return self::$level_text[$this->level];
    }
}
