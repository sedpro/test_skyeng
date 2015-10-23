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

    public $levels = [
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

    /**
     * Fills the entity with random data
     */
    public function randomize()
    {
        $this->name = \Application\Helper\Randomizer::getRandomName();
        $this->email = \Application\Helper\Randomizer::getRandomEmail();
        $this->birthday = \Application\Helper\Randomizer::getRandomDate();
        $this->level = $this->levels[mt_rand(0, 5)];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function getLevel()
    {
        return self::$level_text[$this->level];
    }
}
