<?php

namespace Helpers;

class Request {

    private $params = [];

    function __construct() {
        $this->params = $_GET + $_POST + $_FILES;
    }

    public function all() {
        return $this->params;
    }

    public function get($key, $trim = true) {
        $value = null;
        if (isset($this->params[$key])) {
            $value = $this->params[$key];

            if ($trim)
                $value = trim($value);
        }

        return $value;
    }

    public function set($key, $value) {
        $this->params[$key] = $value;
    }
}