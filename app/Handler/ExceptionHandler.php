<?php

namespace App\Handler;

use ErrorException;
use Exception;
use App\Handler\ViewHandler;

class ExceptionHandler
{

    /**
     * Error handler. Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param int $level Error level
     * @param string $message Error message
     * @param string $file Filename the error was raised in
     * @param int $line Line number in the file
     *
     * @return void
     * @throws ErrorException
     */
    public static function error($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {  // to keep the @ operator working
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception Handler
     *
     * @param Exception $exception  The exception
     * @throws Exception
     */
    public static function exception($exception)
    {
        // Code is 404 (not found) or 500 (general error)
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);

        $debug = $_ENV['DEBUG'] == 'true' ? true : false;
        if ($debug) {
            echo "<h1>Fatal error</h1>";
            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            echo "<p>Err code: '" . $exception->getCode() . "'</p>";
            echo "<p>Messages: '" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        } else {
            $logsDirectory = dirname(__DIR__, 2) . '/logs/';
            if (!is_dir($logsDirectory)) {
                mkdir($logsDirectory);
            }
            
            $log = dirname(__DIR__, 2) . '/logs/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);

            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message '" . $exception->getMessage() . "'";
            $message .= "\nStack trace: " . $exception->getTraceAsString();
            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

            error_log($message, 3, $log);

            ViewHandler::render("errors/{$code}.html");
        }
    }
}