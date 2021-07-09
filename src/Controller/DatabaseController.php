<?php

namespace App\Controller;

use App\Entity\EntityInterface;
use App\Repository\DatabaseRepository;
use App\Response\Response;
use \ReflectionClass;

class DatabaseController implements ControllerInterface {

    private string $cacheFile = 'orm_cache.txt';

    private DatabaseRepository $databaseRepository;

    private $cache = [];

    private array $mapping = [
        'string' => 'varchar(255)',
        'DateTime' => 'DATETIME',
        'int' => 'int',
    ];

    function __construct(DatabaseRepository $databaseRepository)
    {
        $this->databaseRepository = $databaseRepository;
        $this->cache = unserialize(file_get_contents($this->cacheFile)) ? unserialize(file_get_contents($this->cacheFile)) : [];
    }

    function init(): Response {
        $entityList = $this->map();

        $entityClasses = $this->mergeCache($entityList);

        $this->cache = $entityList;

//        $this->saveCache();

        $sql = $this->generateSQL($entityClasses);

        if($sql !== "") {
//            $this->databaseRepository->customQuery($sql);
        }

        return Response::JsonResponse([
            'objects_mapped' => $entityClasses,
            'SQL' => $this->generateSQL($entityClasses)
        ]);
    }

    private function map() {
        $classes = get_declared_classes();
        $entityList = [];
        foreach($classes as $className) {
            $reflect = new ReflectionClass($className);
            if($reflect->implementsInterface(EntityInterface::class)) {
                $publicFields = get_class_methods(new $className());
                $blankClass = new $className();
                $objectVars = [];

                foreach ($publicFields as $field) {
                    if (substr($field, 0, 3) === 'get') {
                        $type = new \ReflectionMethod($blankClass::class, $field);
                        $field = strtolower(str_replace('get', '', $field));
                        $objectVars[$field] = (string) $type->getReturnType();
                    }
                }
                $entityList[strtolower(str_replace('App\\Entity\\', '', $className))] = $objectVars;
            }
        }

        return $entityList;
    }

    private function generateSQL(array $entityChanges) {

        $sql = [];

        foreach($entityChanges['new'] as $entityName => $entityValue) {
            $_sql = 'CREATE TABLE ' . $entityName. '(';

            //TODO NULL VALUES ALLOW
            foreach ($entityValue as $column => $dataType) {
                $nullable = str_contains($dataType, '?');
                $dataType = str_replace('?', '', $dataType);
                if (array_key_exists($dataType, $this->mapping)) {
                    $_sql .= $column . ' ' . $this->mapping[$dataType] . ($nullable ? '' : ' NOT NULL') .',';
                } else if($dataType !== 'array') {
                    throw new \Exception(sprintf('Key %s not found in database mapping!', $dataType));
                }
            }
            $_sql = substr($_sql, 0, -1);
            $_sql .= ');';
            $_sql = str_replace('string', 'varchar(512)', $_sql);
            $sql[] = $_sql;

            $sql[] = "ALTER TABLE ". $entityName . " add PRIMARY KEY (`id`);";
            $sql[] = "ALTER TABLE ". $entityName ." CHANGE `id` `id` INT(11) AUTO_INCREMENT";
        }

        foreach($entityChanges['changed'] as $entityName => $entityValue) {

            foreach ($entityValue['new'] as $column => $dataType) {
                $_sql = 'ALTER TABLE ' . $entityName. ' ADD ';
                if (array_key_exists($dataType, $this->mapping)) {
                    $_sql .= $column . ' ' . $this->mapping[$dataType] . ',';
                } else if($dataType !== 'array') {
                    throw new \Exception(sprintf('Key %s not found in database mapping!', $dataType));
                }

                $_sql = substr($_sql, 0, -1);
                $_sql .= ';';
                $_sql = str_replace('string', 'varchar(512)', $_sql);

                $sql[] = $_sql;
            }

            foreach ($entityValue['removed'] as $column) {
                $sql[] = 'ALTER TABLE ' . $entityName. ' DROP ' . $column . ';';
            }
        }

        return $sql;
    }

    function supports(string $action): bool {
        return $action === 'database';
    }

    private function saveCache() {
        file_put_contents($this->cacheFile, serialize($this->cache));
    }

    private function mergeCache(array $currentCache) {
        $cachedNew = [];
        $cachedChanged = [];

        foreach($currentCache as $cachedObjectKey => $chachedObjectValues) {
            if(!key_exists($cachedObjectKey, $this->cache)) {
                $cachedNew[$cachedObjectKey] = $chachedObjectValues;
            } else if ($currentCache[$cachedObjectKey] !== $this->cache[$cachedObjectKey]) {
                $removedKeys = [];
                $newKeys = [];
                foreach($currentCache[$cachedObjectKey] as $key => $value) {
                    if(!key_exists($key, $this->cache[$cachedObjectKey])) {
                        $newKeys[$key] = $value;
                    }
                }

                foreach($this->cache[$cachedObjectKey] as $key => $value) {
                    if(!key_exists($key, $currentCache[$cachedObjectKey])) {
                        $removedKeys[] = $key;
                    }
                }

                $cachedChanged[$cachedObjectKey] = [
                    'new' => $newKeys,
                    'removed' => $removedKeys,
                ];

                foreach($newKeys as $newKey) {
                    $this->cache[$cachedObjectKey] = $newKey;
                }
                foreach($removedKeys as $key) {
                    try {
                        unset($this->cache[$cachedObjectKey][$key]);
                    } catch (\Error $exception) {
                    }
                }
            }
        }

        return [
            'new' => $cachedNew,
            'changed' => $cachedChanged
        ];
    }
}
