<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'db' => [
        'driver' => 'pdo_mysql',
        'hostname' => '127.0.0.1',
        'username' => '',
        'password' => '',
        'database' => '',
        'driver_options' => [
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ],
    ],
    'storaged' => [
//        'author' => [
//            'alias' => 'author',
//        ],
//
        \Application\Storage\Mysql\Pupil::$alias,
        \Application\Storage\Mysql\Teacher::$alias,
        \Application\Storage\Mysql\TeacherPupil::$alias,
    ],
);
