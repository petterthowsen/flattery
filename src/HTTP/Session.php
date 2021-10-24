<?php

namespace ThowsenMedia\Flattery\HTTP;

class Session {
    
    private static self $instance;

    public static function getInstance()
    {
        if ( ! isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct()
    {
        if ( ! isset($_SESSION))
        session_start();
    }

    public function set(string $key, $value)
    {
        return array_set($key, $value, $_SESSION);
    }

    public function put(string $key, $value)
    {
        return array_put($key, $value, $_SESSION);
    }

    public function has(string $key): bool
    {
        return array_has($key, $_SESSION);
    }

    public function get(string $key)
    {
        return array_get($key, $_SESSION);
    }

    public function remove(string $key)
    {
        return array_unset($key, $_SESSION);
    }

    public function destroy()
    {
        return session_destroy();
    }

}