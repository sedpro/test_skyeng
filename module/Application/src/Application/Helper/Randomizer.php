<?php

namespace Application\Helper;

class Randomizer
{
    /**
     * @return string
     */
    public static function getRandomName()
    {
        return ucfirst(self::strRandom(3,15)) . ' ' . ucfirst(self::strRandom(3,15));
    }

    /**
     * @param int $min
     * @param int $max
     * @return string
     */
    public static function strRandom($min=3, $max=15)
    {
        $length = rand($min, $max);
        return substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz', $length)), 0, $length);
    }

    /**
     * @return string
     */
    public static function getRandomEmail()
    {
        return self::strRandom(3,15). '@' . self::strRandom(3,15) . '.' . self::strRandom(2,3);
    }

    /**
     * @return bool|string
     */
    public static function getRandomDate()
    {
        $min = strtotime('1930-01-01');
        $max = strtotime('2000-01-01');

        $val = rand($min, $max);

        return date('Y-m-d', $val);
    }

    /**
     * @return string
     */
    public static function getRandomPhone()
    {
        return rand(1, 9) . '-' . rand(100, 999) . '-'  . rand(100, 999) . '-' . rand(1000, 9999);
    }
}