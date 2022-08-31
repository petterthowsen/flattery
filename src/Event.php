<?php

namespace ThowsenMedia\Flattery;

class Event
{

    protected int $nextUID = 0;

    protected array $listeners = [];

    protected function nextUID()
    {
        $this->nextUID += 1;
        return $this->nextUID;
    }

    public function listen(string $event, callable $callable, int $priority = 50)
    {
        if ( ! isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        if ( ! isset($this->listeners[$event][$priority])) {
            $this->listeners[$event][$priority] = [];
        }

        $uid = $this->nextUID($priority);
        $this->listeners[$event][$priority][$uid] = $callable;
    }

    public function unListen(string $event, int $uid)
    {
        foreach($this->listeners[$event] as $priority => &$listeners) {
            foreach($listeners as $listenerUID => &$callable) {
                if ($listenerUID === $uid) {
                    unset($this->listeners[$event][$priority][$uid]);
                }
            }
        }
    }

    public function trigger(string $event, ...$arguments)
    {
        if (isset($this->listeners[$event])) {
            foreach($this->listeners[$event] as $priority => &$listeners) {
                foreach($listeners as $callable) {
                    $arguments = call_user_func_array($callable, $arguments);
                }
            }

        }

        return $arguments;
    }

}