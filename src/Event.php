<?php

namespace ThowsenMedia\Flattery;

class Event
{

    protected array $listeners = [];

    public function listen(string $event, callable $callable, int $priority = 50)
    {
        if ( ! isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        if ( ! isset($this->listeners[$event][$priority])) {
            $this->listeners[$event][$priority] = [];
        }

        $this->listeners[$event][$priority][] = $callable;
    }

}