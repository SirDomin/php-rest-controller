<?php

namespace App\Controller;

use App\Entity\EntityInterface;
use App\Repository\DatabaseRepository;
use App\Response\Response;
use \ReflectionClass;

class DatabaseController implements ControllerInterface {

    private $cacheFile = 'orm_cache.txt';

    private $databaseRepository;

    private $cache = [];

    function __construct(DatabaseRepository $databaseRepository)
    {
        $this->databaseRepository = $databaseRepository;
        $this->cache = unserialize(file_get_contents($this->cacheFile)) ? unserialize(file_get_contents($this->cacheFile)) : [];
    }

    function init(): Response {

        $entityList = $this->map();

        $entityClasses = $this->mergeCache($entityList);

        $this->cache = $entityList;

        $this->saveCache();

        $sql = $this->generateSQL($entityClasses);

        if($sql !== "") {
            $this->databaseRepository->customQuery($sql);
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
                $entityList[strtolower(str_replace('App\\Entity\\', '', $className))] = get_object_vars(new $className());
            }
        }

        return $entityList;
    }

    private function generateSQL(array $entityChanges) {

        $sql = [];

        foreach($entityChanges['new'] as $entityName => $entityValue) {
            $_sql = 'CREATE TABLE ' . $entityName. '(';

            foreach ($entityValue as $column => $dataType) {
                if ($dataType === []) {

                } else if (gettype($dataType) === 'DateTime') {
                    $_sql .= $column . ' ' . 'DATETIME,';
                }else {
                    $_sql .= $column . ' ' . gettype($dataType) . ',';
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
                if ($dataType === []) {

                } else if (get_class($dataType) === 'DateTime') {
                    $_sql .= $column . ' ' . 'DATETIME,';
                }else {
                    $_sql .= $column . ' ' . gettype($dataType) . ',';
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
