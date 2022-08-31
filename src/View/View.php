<?php

namespace ThowsenMedia\Flattery\View;

/**
 * A Simple View/Template class that can process includes.
 * 
 * new View('template-file', ['variable' => 'hello, world!'])
 * 
 * Templates are just regular PHP Files.
 * The $this variable is bound to the View instance.
 * 
 * $this->include('partial') includes another view.
 * 
 * the static::$viewsDirectory is the root directory where views are stored.
 * 
 * all views are included with '.php' added.
 * 
 * 
 * @author Petter Thowsen<petter@thowsenmedia.com>
 * @license MIT
 */
class View
{

    public static array $globalVars = [];
    
    private static bool $errorHandlerRegistered = false;

    private string $_file;

    private string $_source;

    public array $_variables = [];

    public static function addGlobalVar(string $variable, $value)
    {
        static::$globalVars[$variable] = $value;
    }

    public static function errorHandler()
    {
        $error = error_get_last();

        if ($error) {
            ob_get_clean();
            echo '<strong>Error</strong>: ' .$error['message'] .'<br>';
            echo 'In file: ' .$error['file'] .' at line ' .$error['line'];
        }
    } 

    private static function registerErrorHandler()
    {
        if (static::$errorHandlerRegistered == false) {
            register_shutdown_function([static::class, 'errorHandler']);
            static::$errorHandlerRegistered = true;
        }
    }

    public function __construct(string $file, array $variables = [])
    {
        static::registerErrorHandler();
        
        $this->_file = $file;
        $this->_variables = $variables;
    }

    public static function make(string $file, array $variables = []): View
    {
        return new static($file, $variables);
    }

    public function overrideSource(string $source)
    {
        $this->_source = $source;
    }

    public function getSource(): string
    {
        return $this->_source;
    }

    public function with($variables): View
    {
        $this->_variables = array_merge($this->_variables, $variables);
        return $this;
    }

    private function include(string $file, array $variables = [])
    {
        $file = dirname($this->_file) .'/' .$file;
        return static::make($file, $variables)->render();
    }

    public function render()
    {
        extract(static::$globalVars);
        extract($this->_variables);
        ob_start();
        if (isset($this->_source)) {
            eval('?>' .$this->_source);
        }else {
            include $this->_file;
        }
        return ob_get_clean();
    }

    public function __toString()
    {
        return $this->render();
    }

}