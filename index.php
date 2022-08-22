<?php

use App\Controllers\{UserController, RoleController};

require_once 'config/app.php';
require_once 'config/database.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// echo $config;
$parts = explode('/', $_SERVER['REQUEST_URI']);

if (strtolower($parts[2]) == 'api') { // api
    $routes = ['users', 'roles'];

    if (!in_array($parts[3], $routes)) {
        http_response_code(404);
        exit;
    }

    $id = $parts[4] ?? null;

    switch ($parts[3]) {
        case 'users':
            $user = new UserController($connection);
            $user->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case 'roles':
            $role = new RoleController($connection);
            $role->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;
    }


} else {// mvc

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