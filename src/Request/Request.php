<?php

declare(strict_types=1);

namespace App\Request;

final class Request
{
    private array $data;

    private array $server;

    public function __construct()
    {
        $this->data = $_REQUEST;
        $this->server = $_SERVER;
    }

    public function get(string $key) {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function data(): array {
        return $this->data;
    }

    public function url(): string {
        return $this->server['REQUEST_URI'];
    }

    public function method(): string {
        return $this->server['REQUEST_METHOD'];
    }
}
