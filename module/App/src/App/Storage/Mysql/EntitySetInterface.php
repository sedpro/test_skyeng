<?php

namespace App\Storage\Mysql;

use Zend\Db\ResultSet\ResultSetInterface;

use App\Entity\SetInterface;

interface EntitySetInterface extends ResultSetInterface, SetInterface
{
}
