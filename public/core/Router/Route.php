<?php

class Route{
    public string $controller;
    public string $resource;
    public string $action;
    public string $method;
    public array $params;

    private function __construct(string $controller, string $resource, string $action, string $method, array $params)
    {
        $this->controller = $controller;
        $this->resource = $resource;
        $this->action = $action;
        $this->method = $method;
        $this->params = $params;
    }

    public static function get(string $controller, string $resource, string $action, array $params): void
    {
        $router = Router::getInstance();
        $router->addRoute(new self($controller, $resource, $action, 'GET', $params));
    }

    public static function post(string $controller, string $resource, string $action, array $params): void
    {
        $router = Router::getInstance();
        $router->addRoute(new self($controller, $resource, $action, 'POST', $params));
    }

    public static function put(string $controller, string $resource, string $action, array $params): void
    {
        $router = Router::getInstance();
        $router->addRoute(new self($controller, $resource, $action, 'PUT', $params));
    }

    public static function delete(string $controller, string $resource, string $action, array $params): void
    {
        $router = Router::getInstance();
        $router->addRoute(new self($controller, $resource, $action, 'DELETE', $params));
    }
}