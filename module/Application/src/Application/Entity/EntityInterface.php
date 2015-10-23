<?php

namespace Application\Entity;

interface EntityInterface
{
    public function getFields();
    public function exchangeArray($data);
    public function getId();
}