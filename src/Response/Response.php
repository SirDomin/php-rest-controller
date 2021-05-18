<?php

namespace App\Response;

class Response
{
    private string $header = '';
    private $data;
    private int $code;

    public function __construct(string $header, $data, $code)
    {
        $this->header = $header;
        $this->data = $data;
        $this->code = $code;
    }

    public function response(): void
    {
        header($this->header);
        http_response_code($this->code);
        die(json_encode($this->data, JSON_UNESCAPED_SLASHES));
    }

    public static function JsonResponse($data, $code = 200): self
    {
        return new self('Content-Type: application/json;', $data, $code);
    }
}