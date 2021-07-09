<?php

namespace App\Entity;

class Entity
{
    private function serialize(): array
    {
        $serialized = [];

        foreach (get_class_methods($this) as $field) {
            if (substr($field, 0, 3) === 'get') {
                $serialized[strtolower(str_replace('get', '', $field))] = $this->$field();
            }
        }

        return $serialized;
    }

    public function properties(): array
    {
        $reflection = new \ReflectionClass(get_class($this));

        $properties = array_keys($reflection->getDefaultProperties());

        while ($parentClass = $reflection->getParentClass()) {
            $reflection = new \ReflectionClass($parentClass->getName());

            $properties = array_merge($properties, array_keys($reflection->getDefaultProperties()));
        }

        return $properties;
    }

    public function propertiesDocs(): array
    {
        $reflection = new \ReflectionClass(get_class($this));

        $properties = $reflection->getProperties();

        while ($parentClass = $reflection->getParentClass()) {
            $reflection = new \ReflectionClass($parentClass->getName());

            $properties = array_merge($properties, $reflection->getProperties());
        }

        return $properties;
    }

    public function setValue($method, string $type, $value): void
    {
        switch ($type) {
            case "string":
                $this->$method((string) $value);
                break;
            case "int" :
                $this->$method((int) $value);
                break;
            case "?DateTime":
            case "DateTime":
                $this->$method(new \DateTime($value));
                break;
            default:
                $this->$method($value);
        }
    }

    public function __get(string $name)
    {
        $method = sprintf('get%s', ucfirst($name));
        return $this->$method();
    }


    public function __invoke()
    {
        $serialized = $this->serialize();

        $serialized['entity'] = get_class($this);

        return $serialized;
    }

    public function toArray(): array
    {
        return $this->serialize();
    }

    public function tableName(): string
    {
        return strtolower(str_replace('App\\Entity\\', '', get_class($this)));
    }
}