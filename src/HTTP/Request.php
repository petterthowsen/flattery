<?php

namespace ThowsenMedia\Flattery\HTTP;

class Request {

    private string $method;

    private string $rawQuery = '';

    private array $segments = [];
    
    public function __construct()
    {
        if ( ! FLATTERY_CONSOLE) {
            $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        }
        
        $this->rawQuery = $_GET['_flattery_query'] ?? '';
        if (strlen($this->rawQuery) > 0) {
            $this->segments = explode('/', $this->rawQuery);
        }
    }

    public function segment($number): ?string
    {
        return $this->segments[$number] ?? null;
    }

    public function getSegments(): array
    {
        return $this->segments;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function starts_with(string $uri): bool
    {
        return str_starts_with($this->rawQuery, $uri);
    }

    public function is(string $uri): bool
    {
        return trim($this->rawQuery, '/') == trim($uri, '/');
    }

}