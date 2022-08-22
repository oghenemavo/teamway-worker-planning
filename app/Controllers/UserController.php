<?php

namespace App\Controllers;

use App\Models\User;
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
                $user = new User($this->pdo);
                // var_dump($user->read());
                echo json_encode($user->read());
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
                $user = new User($this->pdo);
                
                echo json_encode([
                    'status' => true,
                    'message' => 'Users fetched Successfully',
                    'data' => $user->read()
                ]);
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