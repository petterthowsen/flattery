<?php

namespace ThowsenMedia\Flattery\HTTP;

class Response {

    public static function make(string $content = '', int $statusCode = 200): self
    {
        $response = new static();
        $response->setContent($content);
        $response->setStatusCode($statusCode);
        return $response;
    }

    public static function redirect(string $to)
    {
        $response = new static();
        $response->setHeader('Location', url($to));
        $response->setHeader('Status-code', '303');
        return $response;
    }

    private array $headers = [];

    private $content;
    
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function setStatusCode(int $code)
    {
        $this->setHeader('Status-code', $code);
    }

    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }

    public function send()
    {
        foreach($this->headers as $key => $value) {
            header($key .': ' .$value);
        }
        
        echo $this->content;
    }

    public function with(string $flashKey, string $flashMessage): self
    {
        session()->set('flash.' .$flashKey, $flashMessage);
        return $this;
    }

    public function withMessage($flashMessage): self
    {
        session()->put('flash.message', $flashMessage);
        return $this;
    }

}