<?php

namespace Application\Helper;

class Time
{
    /**
     * Get script execution time
     *
     * First call remembers the timestamp.
     * Every other call returns time passed from last timestamp and also remembers a new timestamp.
     *
     * @return float
     */
    public static function check()
    {
        static $previous = null;

        $now = microtime(true);

        if (is_null($previous)) {
            $previous = microtime(true);
            $time = 0;
        } else {
            $time = $now - $previous;
        }

        $previous = $now;

        return $time;
    }
}