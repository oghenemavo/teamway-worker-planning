<?php
use App\Models\BaseModel;
echo "start up";

require_once 'config/app.php';
require_once 'config/database.php';

// echo $config;
// $parts = explode('/', $_SERVER['REQUEST_URI']);

$i = new BaseModel();

echo '<pre>' . var_export($i->_connection, true) . '</pre>';