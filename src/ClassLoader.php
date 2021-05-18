<?php

declare(strict_types=1);

namespace App;

use App\Generator\Generator;
use App\Parser\XmlParser;

final class ClassLoader
{
    private $config = [];

    private array $classesLoaded = [];

    public function __construct()
    {
        $this->config = include 'src/config/config.php';
        $this->autoload();
    }

    private function autoload(): void {
        foreach ($this->config as $serviceName => $service) {
            if (isset($service['autoload']) && $service['autoload'] === true) {
                $this->load($serviceName);
            }
        }
    }

    public function addObject($object) {
        $this->classesLoaded[get_class($object)] = $object;
    }

    /**
     * @throws \Exception
     */
    public function load(string $className) {
        if (array_key_exists($className, $this->classesLoaded)) {
            return $this->classesLoaded[$className];
        }

        if (array_key_exists($className, $this->config)) {
            $classArguments = [];

            foreach ($this->config[$className]['services'] as $classToLoad) {
                $classArguments[] = $this->load($classToLoad);
            }

            try {
                $this->classesLoaded[$className] = new $className(...$classArguments);
            } catch(\Error $error) {
                Generator::generateMockController($className);
//                throw new \Exception(sprintf('Service with name %s does not exist, did you forget to create one?', $className));
            }
        } else {
            Generator::generateMockController($className);
//            throw new \Exception(sprintf('Service with name %s not found in src/config/config.php', $className), 500);
        }

        return $this->classesLoaded[$className];
    }
}
