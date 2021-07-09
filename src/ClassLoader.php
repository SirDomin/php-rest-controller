<?php

declare(strict_types=1);

namespace App;

final class ClassLoader
{
    private $config = [];

    private array $classesLoaded = [];

    private array $filesLoaded = [];

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

        $this->loadEntities();
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
                $this->loadFile($classToLoad);
                $classArguments[] = $this->load($classToLoad);
            }

            $this->loadFile($className);

            try {
                $this->classesLoaded[$className] = new $className(...$classArguments);
            } catch(\Error $error) {
                // generate controller if doesnt exist
                $this->classesLoaded[$className] = new $className();
            }
        } else {
            $this->classesLoaded[$className] = new $className();
        }

        return $this->classesLoaded[$className];
    }

    public function getFilesLoaded()
    {
        return sizeof($this->filesLoaded);
    }

    private function loadFile(string $className, ?string $previousClassName = null): bool
    {
        $classExploded = explode('\\', $className);
        $class = end($classExploded);

        $className = str_replace('.php', '', $className);
        $className = str_replace(' ', '', $className);
        $fileName = str_replace('App', 'src', implode('/', $classExploded)). '.php';
        $fileName = str_replace(' {', '', $fileName);


        if (!array_key_exists($fileName, $this->filesLoaded)) {
            $file = '';
            if(file_exists($fileName)){
                $file = file_get_contents($fileName);
            }
            if (str_contains($file, 'use ')) {
                $this->getUse($file);
            }
            if (str_contains($file, 'implements ')) {
                $classToLoad = explode('implements ', $file);
                $classToLoad = explode("\r", $classToLoad[1]);

                $this->loadFile(str_replace($class, $classToLoad[0], $className));
            }

            if(class_exists($className)) {
                $this->classesLoaded[] = $className;
                return true;
            }

            try {
                if(!file_exists($fileName)){
                    return false;
                }

                include($fileName);
                $this->filesLoaded[$fileName] = true;
                return true;
            } catch (\Error $error) {

                if(str_contains($file, 'extends')) {
                    $classToLoad = explode('extends ', $file);
                    $classToLoad = explode("\r", $classToLoad[1]);

                    $classToLoad = explode(' implements', $classToLoad[0]);
                    $previousClassName = $fileName;

                    if (!$this->loadFile(str_replace($class, $classToLoad[0], $className), $previousClassName)) {
                        $this->loadFile(str_replace($class, $classToLoad[0], $className), $previousClassName);
                    };
                }
                return false;
            }
        }
        return true;
    }

    private function getUse(string $file)
    {
        $usages = explode('use ', $file);
        array_shift($usages);
        foreach ($usages as $use) {
            $use = explode(';', $use)[0];
            $this->loadFile($use);
        }
    }

    private function getInterface(string $file, $className)
    {
        $usages = explode('implements ', $file);
        array_shift($usages);
        foreach ($usages as $use) {
            $use = explode("\r", $use)[0];
            $this->loadFile($use);
        }
    }

    //TODO generator for namespaces
    private function loadEntities()
    {
        $loads = [];
        foreach (glob("src/Entity/*.php") as $filename)
        {

            $this->loadFile($this->getFullClassName($filename));
            $loads[] = $this->getFullClassName($filename);
////            $className =
//            $className = str_replace('.php', '', $className);
//            $className = str_replace(' ', '', $className);
//            $fileName = str_replace('App', 'src', implode('/', $filename)). '.php';
//            $this->filesLoaded[$filename] = true;
//            include $filename;
        }

//        dd($this->filesLoaded);
//        dd(glob("src/Entity/*.php"));
//        dd(get_declared_classes());
        dd($loads);
    }

    private function getFullClassName(string $fileName): string
    {
        $fileContents = file_get_contents($fileName);

        $tokens = token_get_all($fileContents);

        $namespace = 'GLOBAL';
        foreach ($tokens as $token) {
            if (!is_string($token)) {
                list($id, $text) = $token;
                if ($id == T_NAME_QUALIFIED) {
                    $namespace = $text;
                }
                if ($id == T_STRING) {
                    return $namespace.'\\'.$text;
                }
            }
        }

        return '';
    }
}
