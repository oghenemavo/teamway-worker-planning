<?php

namespace App\Controllers;

use PDO;

class UserController extends BaseController
{
    public function __construct(private PDO $pdo)
    {
    }
    
    public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            
            $this->processResourceRequest($method, $id);
            
        } else {
            
            $this->processCollectionRequest($method);
            
        }
    }
    
    private function processResourceRequest(string $method, string $id): void
    {
        switch ($method) {
            case 'GET':
                echo json_encode([1]);
                break;
                
            default:
                http_response_code(405);
                header('Allow: GET');
        }
    }
    
    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case 'GET':
                echo json_encode([1]);
                break;
                
            case 'POST':
                echo json_encode([1]);
                break;
            
            default:
                http_response_code(405);
                header('Allow: GET, POST');
        }
    }

}