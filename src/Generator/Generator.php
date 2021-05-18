<?php

namespace App\Generator;

class Generator
{
    public static function generateMockController(string $namespace): void
    {
        $mock = file_get_contents(sprintf('./templates/%s', 'Controller/MockController.txt'));

        $mockData = explode('\\', $namespace);
        $appName = $mockData[0];
        unset($mockData[0]);
        $className = $mockData[array_key_last($mockData)];

        $mock = str_replace('{{CLASS_NAME}}', $className, $mock);
        $mock = str_replace('{{ENDPOINT_NAME}}', "\"$className\"", $mock);

        $directory = implode('/', $mockData);

        $controllerFile = file('./src/config/controller.php');

        $eof = "];\r\n";
        $counter = 0;
        foreach ($controllerFile as $line) {
            if(str_contains($line, $namespace)) {
                throw new \Exception(sprintf('service with namespace %s is already in config files', $namespace));
                return;
            }
            if (str_contains($line, $eof)) {
                unset($controllerFile[$counter]);
                unset($controllerFile[$counter - 1]);
            }
            $counter++;
        }
        $controllerFile[] = "    ],\r\n";
        $controllerFile[] = "    '".$namespace."' => [\r\n";
        $controllerFile[] = "        'services' => []\r\n";
        $controllerFile[] = "    ]\r\n";
        $controllerFile[] = $eof;

        $newFile = fopen('./src/config/controller.php', 'w');
        fwrite($newFile, implode('', $controllerFile));

        file_put_contents(sprintf('./src/%s.php', $directory), $mock);
    }
}