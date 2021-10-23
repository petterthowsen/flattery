<?php

namespace ThowsenMedia\Flattery\HTTP;

class Response {

    public static function make(string $content): self
    {
        $response = new static();
        $response->setContent($content);
        return $response;
    }

    private array $headers = [];

    private $content;
    
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function send()
    {
        foreach($this->headers as $key => $value) {
            header($key .': ' .$value);
        }
        
        echo $this->content;
    }

}