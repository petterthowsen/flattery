<?php

namespace ThowsenMedia\Flattery;

class Container {

    protected array $bindings = [];

    public function bindClosure(string $name, string $class, callable $callable, bool $singleton = false)
    {
        $this->bindings[$name] = [
            'class' => $class,
            'singleton' => $singleton,
            'callable' => $callable,
            'instance' => null,
        ];
    }

    public function bind(string $name, string $class, bool $singleton = false)
    {
        $this->bindings[$name] = [
            'class' => $class,
            'singleton' => $singleton,
            'instance' => null
        ];
    }

    public function bindInstance(string $name, object $instance)
    {
        $this->bindings[$name] = [
            'class' => get_class($instance),
            'singleton' => true,
            'instance' => $instance,
        ];
    }

    public function hasBinding(string $name)
    {
        return isset($this->bindings[$name]);
    }

    public function isClassBound(string $class)
    {
        foreach($this->bindings as $name => $binding)
        {
            if ($binding['class'] === $class) {
                return true;
            }
        }

        return false;
    }

    private function create(string $name): object
    {
        $binding = & $this->bindings[$name];

        if (isset($binding['callable'])) {
            $instance = call_user_func($binding['callable']);
            
            if ( ! is_object($instance)) {
                throw new \Exception("Bound callable failed to return an object.");
            }

            $expected = $binding['class'];
            $instanceClass = get_class($instance);

            if ($instanceClass !== $binding['class']) {
                throw new \Exception("Binding callable returned an instance of $instanceClass. It should return an instance of $expected");
            }

            if ($binding['singleton']) {
                $binding['instance'] = $instance;
            }

            return $instance;
        }else {
            if ($binding['singleton'] == true) {
                $class = $binding['class'];

                if ( ! isset($binding['instance'])) {
                    $instance = new $class();
                    $binding['instance'] = $instance;
                }else {
                    $instance = $binding['instance'];
                }

                return $instance;
            }else {
                return new $binding['class']();
            }
        }
    }

    public function get(string $name): object
    {
        if ( ! $this->hasBinding($name)) {
            throw new \Exception("$name is not bound to any class.");
        }

        $binding = $this->bindings[$name];

        if (isset($binding['instance'])) {
            return $binding['instance'];
        }else {
            return $this->create($name);
        }
    }

    public function __get(string $name)
    {
        return $this->get($name);
    }

    public function __set(string $name, $val)
    {
        if (is_string($val)) {
            $this->bind($name, $val, false);
        }else if (is_object($val)) {
            $this->bindInstance($name, $val);
        }else {
            throw new \Exception("Cannot magically bind $name to a value other than a class name or instance.");
        }
    }

}