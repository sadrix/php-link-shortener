<?php

use Database\Database;
use Helpers\Request;

/**
 * create standard json response and 
 */
function response($code = 200, $message = 'success!', $data = null) {
    header('Content-Type: application/json; charset=utf-8');
    
    echo json_encode([
        'code' => $code,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * get value of config based on file path and config key in config directory
 */
function config($name, $default = null) {
    $parts = explode('/', $name);
    if ($parts && is_array($parts) && count($parts) > 1) {
        $key = array_pop($parts);
        $path = implode('/', $parts);
        $file_path = '../config/' . $path . '.json';
        try {
            $json = file_get_contents($file_path);
            $configs = (object) json_decode($json, true);
            return property_exists($configs, $key) ? $configs->$key : $default;
        } catch (Exception $e) {
            return $default;
        }
    } else 
        return $default;
}

/**
 * create full url based on app url
 */
function url($path, array $params = []) {
    $url = config('app/url') . $path;

    if ($params) {
        $queries = [];
        
        foreach ($params as $key => $value)
            $queries[] = $key . "=" . urlencode($value);
        
        $url .= '?' . implode('&', $queries);
    }

    return $url;
}

// global request param
global $request;
$request = new Request();

function request() {
    global $request;
    return $request;
}

// global database param
global $db;
$db = new Database();

function db() {
    global $db;
    return $db;
}

function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
{
    $string = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));

    if (!$capitalizeFirstCharacter)
        $string[0] = strtolower($string[0]);

    return $string;
}

function randomString($length = 16) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    
    for ($i = 0; $i < $length; $i++) 
        $string .= $chars[rand(0, strlen($chars) - 1)];
    
    return $string;
}