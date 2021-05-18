<?php

namespace App\Repository;

class EntityRepository extends DatabaseRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function save(&$object): bool
    {
        /** @var \ReflectionProperty $property */
        foreach ($object->propertiesDocs() as $property) {
            if($comment = $property->getDocComment()) {
                if (preg_match_all('/@[a-z]+/m', $comment, $matches)){
                    foreach ($matches[0] as $match) {
                        if (str_replace('@', '', $match) === 'unique') {
                            $obj = parent::getRecordData($object->tableName(), $object->toArray()[$property->getName()], $property->getName());
                            if ($obj && $obj->getId() !== $object->getId()) {
                                throw new \Exception(sprintf('%s class value %s must be unique', get_class($object), $property->getName()));
                            }
                        }
                    }
                }
            }
        }
        if ($object->getId() > 0) {
            $id = parent::updateData($object->tableName(), $object->toArray(), 'id');
        } else {
            $id = parent::insertRecordData($object->tableName(), $object->toArray(), true);
            $object->setId($id);
        }

        if ($id === 0) {
            throw new \Exception('Could not insert data');
        }
        return true;
    }
}