<?php

namespace ThowsenMedia\Flattery\HTTP;

use ThowsenMedia\Flattery\CMS;

class Kernel {


    private array $handlers = [];

    private Request $request;

    private array $handle_callables = [];
    private int $handle_current = -1;
    
    public function handle(Request $request)
    {
        $this->request = $request;

        event()->trigger('flattery.kernel.handle.before', $request);
        
        $this->handle_callables = [];

        foreach($this->handlers as $priority => $handlers) {
            foreach($handlers as $name => $callable) {
                $this->handle_callables[] = ['name' => $name, 'callable' => $callable];
            }
        }
        
        $this->handle_current = -1;
        
        $self = $this;

        return $this->next();
    }

    public function next()
    {
        if ($this->handle_current < count($this->handle_callables) - 1) {
            $this->handle_current += 1;
            $handler = $this->handle_callables[$this->handle_current];
            
            $callable = $handler['callable'];
            $name = $handler['name'];
            return $callable($this->request, [$this, 'next']);
        }else {
            return false;
        }
    }

    public function attachHandler(string $name, callable $handler, int $priority = 0)
    {
        if ( ! isset($this->handlers[$priority])) {
            $this->handlers[$priority] = [];
        }

        $this->handlers[$priority][$name] = $handler;
    }

    public function removeHandler(string $name)
    {
        foreach($this->handlers as &$priority) {
            foreach($priority as $handlerName => $handler) {
                if ($handlerName === $name) {
                    unset($priority[$handlerName]);
                }
            }
        }
    }
    
}