<?php

namespace ThowsenMedia\Flattery\HTTP\Routing;

class Router {

    /**
     * @property Route[]
     */
    protected array $routes = [];

    private function makeRoute(string $httpMethod, string $uri, $target)
    {
        $route = new Route($httpMethod, $uri, $target);
        $this->routes[$httpMethod .':' .$route->getUri()] = $route;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function get(string $uri, $target)
    {
        return $this->makeRoute('GET', $uri, $target);
    }

    public function post(string $uri, $target)
    {
        return $this->makeRoute('POST', $uri, $target);
    }

}