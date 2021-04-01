<?php

class Router
{
    private $routes;
    private $matchRoute;
    private $classFunction;
    private $uri;

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function run() {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        foreach ($this->routes as $matchRoute => $classFunction) {
            if ($this->parseUrl($url, $matchRoute)) {
                $this->bindData($url, $matchRoute, $classFunction);
                $this->loadClassFromParams();
                return;
            }
        }
        $this->notFoundError();
    }

    private function parseUrl($url, $match) {
        $pattern = $this->createPatterFromMatch($match);
        return preg_match($pattern, $url);
    }

    private function createPatterFromMatch($match) {
        $pattern = str_replace("/", "\/", $match);
        $pattern = preg_replace("/:\w+/i", "\w+", $pattern);
        $pattern = "/^" . $pattern . "$/i";
        return $pattern;
    }

    private function notFoundError() {
        Serializer::Error(404, 'Page not found');
    }

    private function autoloadClass($class) {
        include '/controllers/' . $class . '.php';
    }

    private function LoadParams() {
        $matcher = explode('/', $this->matchRoute);
        $urlArr = explode('/', $this->uri);
        $params = [];

        foreach ($matcher as $key => $match) {
            if (preg_match('/:\w+/', $match)) {
                $arrKey = trim($match, ':');
                $params[$arrKey] = $urlArr[$key];
            }
        }
        if (!$params) {
            return null;
        }
        return $params;
    }

    private function bindData($url, $matchRoute, $classFunction) {
        $this->uri = $url;
        $this->matchRoute = $matchRoute;
        $this->classFunction = $classFunction;
    }

    private function loadClassFromParams() {
        list($class, $method) = explode("::", $this->classFunction);
        $this->autoloadClass($class);
        $params = $this->loadParams();
        call_user_func(array($class, $method), $params);
    }
}