<?php

use App\Handler\DatabaseHandler;

$connection = null;

if ($config->get('database.driver')) {
    $db = new DatabaseHandler(
        $config->get('database.driver'),
        $config->get('database.host'), 
        $config->get('database.database'), 
        $config->get('database.username'), 
        $config->get('database.password')
    );
    $connection = $db->connect();
}