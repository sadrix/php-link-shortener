<?php

namespace Bootstrap;

class Router {
    private $router;

    function __construct() {
        $router = new \Bramus\Router\Router();
        require_once(__DIR__ . '../../routes/api.php');
        $router->run();
    }

    public function run() {
        $this->router->run();
    }
}
