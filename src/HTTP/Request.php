<?php

namespace ThowsenMedia\Flattery\HTTP;

class Request {

    private string $rawQuery = '';

    private array $segments = [];

    public function __construct()
    {
        $this->rawQuery = $_GET['_flattery_query'] ?? '';
        if (strlen($this->rawQuery) > 0) {
            $this->segments = explode('/', $this->rawQuery);
        }
    }

    public function segment($number): ?string
    {
        return $this->segments[$number] ?? null;
    }

}