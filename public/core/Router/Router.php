<?php

class Router{
    private static Router $instance;

    private array $routes;

    private function __construct()
    {
        $this->routes = array();
    }

    public static function getInstance(): Router
    {
        if(isset(self::$instance)) return self::$instance;

        self::$instance = new self();
        return self::$instance;
    }

    public function addRoute(Route $route): void
    {
        $this->routes[$route->resource][$route->method] = $route;

        register_rest_route('micerule/v1', "/(?P<resource>[a-zA-Z0-9_-]+)", array(
            'methods' => $route->method,
            'callback' => [$this, 'delegate'], // Adjust permissions as needed
        ));
    }

    public function delegate(WP_REST_Request $request): WP_REST_Response
    {
        $resource = $request->get_param("resource");
        $route = $this->routes[$resource][$request->get_method()];
        if($route === null) return new WP_REST_Response("", 404);

        $params = array();
        foreach($route->params as $param){
            $params[] = $request->get_param($param);
        }

        $controllerClass = $route->controller;
        $action = $route->action;

        $controller = new $controllerClass();

        $response = $controller->$action(...$params);

        if(!$response instanceof WP_REST_Response) return new WP_REST_Response($response);
        return $response;
    }
}