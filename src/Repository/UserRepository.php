<?php

namespace App\Repository;

use App\Entity\User;

class UserRepository extends EntityRepository
{
    private string $tableName = 'user';

    public function __construct()
    {
        parent::__construct();
    }

    public function findOneBy($field, $value): ?object
    {
        return parent::getRecordData($this->tableName, $value, $field);
    }
}
