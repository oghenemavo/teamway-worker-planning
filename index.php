<?php

use App\Controllers\UserController;
use App\Handler\DatabaseHandler;
// use App\Controllers\UserController;

// $a = new UserController();
// echo "start up";

require_once 'config/app.php';
require_once 'config/database.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



// echo $config;
$parts = explode('/', $_SERVER['REQUEST_URI']);

$db = DatabaseHandler::getInstance()->connect('mysql', 'localhost', 'teamway_db', 'root', '');

if (strtolower($parts[2]) == 'api') {
    $routes = ['users'];

    if (!in_array($parts[3], $routes)) {
        http_response_code(404);
        exit;
    }

    $id = $parts[4] ?? null;

    $controller = new UserController($db);

    $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
} else {

}


function validUriControllers()
{
    $allControllers = array_filter(get_declared_classes(), function($value) {
        return strchr($value, 'App\\Controllers\\');
    });

    return array_map(function($value) {
        return str_ireplace('Controller', '', str_ireplace('App\Controllers\\', '', strtolower($value)));
    }, $allControllers);
}