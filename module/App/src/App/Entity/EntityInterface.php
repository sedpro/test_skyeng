<?php

namespace App\Entity;

interface EntityInterface
{
    /**
     * Принимает данные для объекта в виде массива
     *
     * @param array $data
     */
    public function exchangeArray(array $data);

    /**
     * Отдает значения объекта в виде ассоциативного массива
     *
     * @return array
     */
    public function getValues();

    /**
     * Отдает имя свойства хранящее значение ключевое значение
     *
     * @return string
     */
    public function getKeyName();

    /**
     * Отдает значение ключевого свойства
     *
     * @return mixed
     */
    public function getKeyValue();
}