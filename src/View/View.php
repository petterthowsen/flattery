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

    private static $viewsDirectory;

    public static $globalVars = [];
    
    private $_file;

    public $_variables = [];

    public static function setViewsDirectory(string $directory)
    {
        static::$viewsDirectory = $directory;
    }

    public static function addGlobalVar(string $variable, $value)
    {
        static::$globalVars[$variable] = $value;
    }

    public function __construct(string $file, array $variables = [])
    {
        $this->_file = static::$viewsDirectory .'/' .$file .'.php';
        $this->_variables = $variables;
    }

    public static function make(string $file, array $variables = []): View
    {
        return new static($file, $variables);
    }

    private function include(string $file, array $variables = [])
    {
        return static::make($file, $variables)->render();
    }

    public function render()
    {
        extract($this->_variables);
        extract(static::$globalVars);
        ob_start();
        include $this->_file;
        return ob_get_clean();
    }

}