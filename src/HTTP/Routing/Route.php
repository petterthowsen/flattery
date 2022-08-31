<?php

namespace ThowsenMedia\Flattery\HTTP\Routing;

use ThowsenMedia\Flattery\HTTP\Request;

class Route {

    private string $uri;

    /**
     * @property string[]
     */
    private array $segments = [];

    private string $httpMethod = 'GET';

    private string $controllerClassName;

    private string $controllerMethodName;

    private $callable;

    public function __construct(string $httpMethod, string $uri, $target = null)
    {
        $this->httpMethod = $httpMethod;
        $this->uri = trim($uri, '/');

        if (strlen($this->uri) > 0)
            $segments = explode('/', $this->uri);
        else
            $segments = [];


        foreach($segments as $segment) {
            $type = 'literal';

            if (str_starts_with($segment, '{') and str_ends_with($segment, '}')) {
                $type = 'parameter';
            }
            
            $this->segments[$segment] = [
                'type' => $type,
                'required' => str_ends_with($segment, '?}') == false,
            ];
        }
        
        if (is_callable($target)) {
            $this->toCallable($target);
        }else if (is_array($target) and count($target) == 2) {
            list($class, $method) = $target;
            if (is_object($class)) {
                if ( ! method_exists($class, $method)) {
                    $className = get_class($class);
                    throw new \Exception("Cannot route to nonexistent method $method on $className!");
                }
            }else {
                $this->toController($class, $method);
            }
        }
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function toController(string $class, string $method)
    {
        $this->controllerClassName = $class;
        $this->controllerMethodName = $method;
    }

    public function toCallable(callable $callable)
    {
        $this->callable = $callable;
    }

    public function extractParametersFromRequest(Request $request): array
    {
        $parameters = [];

        $i = 0;
        foreach($this->segments as $segment => $options) {
            $requestSegment = $request->segment($i);
            if ($options['type'] == 'parameter') {
                if ($requestSegment !== null) {
                    $parameters[$segment] = $requestSegment;
                }
            }
        }

        return $parameters;
    }

    public function numberOfRequiredSegments(): int
    {
        $n = 0;

        foreach($this->segments as $segment => $options) {
            if ($options['type'] == 'literal') {
                $n += 1;
            }else if ($options['type'] == 'parameter') {
                if ($options['required']) {
                    $n += 1;
                }
            }
        }

        return $n;
    }

    public function matches(Request $request): bool
    {
        if ($request->getMethod() == $this->httpMethod) {
            $numberOfRequestSegments = count($request->getSegments());
            
            if ($numberOfRequestSegments < $this->numberOfRequiredSegments()) {
                return false;
            }else if ($numberOfRequestSegments > count($this->segments)) {
                return false;
            }

            $i = 0;
            foreach($this->segments as $segment => $options) {
                $requestSegment = $request->segment($i);
                
                if ($options['type'] == 'literal') {
                    if ($requestSegment == null OR $requestSegment !== $segment) {
                        return false;
                    }
                }else if ($requestSegment == null and $options['required']) {
                    return false;
                }

                $i ++;
            }

            return true;
        }

        return false;
    }

    /**
     * Instantiates and calls the controller with the given parameters.
     * @return Response|string|mixed
     */
    public function call(array $parameters = [])
    {
        if (isset($this->callable)) {
            return call_user_func_array($this->callable, $parameters);
        }else {
            $controller = new $this->controllerClassName();
            return call_user_func_array([$controller, $this->controllerMethodName], $parameters);
        }
    }

    public function __toString(): string
    {
        $str = $this->httpMethod .':';

        if (count($this->segments) == 0) {
            $str .= '/';
        }else {
            foreach($this->segments as $segment => $options) {
                $str .= $segment .'/';
            }
            
            $str = rtrim($str, '/');
        }

        
        $str .= ' -> ';

        if ($this->callable != null) {
            $str .= 'callable';
        }else {
            $str .= $this->controllerClassName .'@' .$this->controllerMethodName;
        }

        return $str;
    }

}