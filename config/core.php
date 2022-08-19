<?php

use Dotenv\Dotenv;
use League\Config\Configuration;
use Nette\Schema\Expect;

$rootDirectory = dirname(__DIR__, 1);

require_once $rootDirectory . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable($rootDirectory);
$dotenv->load();


// Define your configuration schema
$config = new Configuration([
    'database' => Expect::structure([
        'driver' => Expect::anyOf('mysql', 'postgresql', 'sqlite')->required(),
        'host' => Expect::string()->default('localhost'),
        'port' => Expect::int()->min(1)->max(65535),
        'ssl' => Expect::bool(),
        'database' => Expect::string()->required(),
        'username' => Expect::string()->required(),
        'password' => Expect::string()->nullable(),
    ]),
    'logging' => Expect::structure([
        'enabled' => Expect::bool()->default($_ENV['DEBUG'] == true),
        // 'file' => Expect::string()->deprecated("use logging.path instead"),
        // 'path' => Expect::string()->assert(function ($path) { return \is_writeable($path); })->required(),
    ]),
]);

$config->merge([
    'database' => [
        'driver' => $_ENV['DB_CONNECTION'],
        'host' => $_ENV['DB_HOST'],
        'port' => (int) $_ENV['DB_PORT'],
        'database' => $_ENV['DB_DATABASE'],
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD'],
    ],
]);