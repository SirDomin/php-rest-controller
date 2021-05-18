<?php

declare(strict_types=1);

namespace App\Request;

final class Request
{
    private array $data;

    private array $server;

    private array $header;

    public function __construct()
    {
        $this->data = $_REQUEST;
        $this->server = $_SERVER;
        $this->header = getallheaders();
    }

    public function get(string $key, bool $require = false) {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        if($require === true) {
            throw new \Exception(sprintf('%s field is required', $key), 400);
        }

        return null;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function getAuth(): ?string
    {
        if (isset($this->header['Authorization'])) {
            return $this->header['Authorization'];
        }

        return null;
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
