<?php

use App\Handler\DatabaseHandler;

$db = DatabaseHandler::getInstance();

if ($config->get('database.driver')) {
    $pdo = $db->connect(
        $config->get('database.driver'), 
        $config->get('database.host'), 
        $config->get('database.database'), 
        $config->get('database.username'), 
        $config->get('database.password')
    );
}