<?php

require_once 'core.php';

global $config;

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('\App\Handler\ExceptionHandler::error');
set_exception_handler('\App\Handler\ExceptionHandler::exception');
