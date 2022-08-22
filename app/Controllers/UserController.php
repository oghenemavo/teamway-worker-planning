<?php

namespace App\Controllers;

use App\Models\User;
use Rakit\Validation\Validator;
use App\Utils\Rules\UniqueRule;
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

                echo json_encode([
                    'status' => true,
                    'message' => 'User fetched Successfully',
                    'data' => $user->retrieve()->where('id', $id)->get()
                ]);
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
                    'data' => $user->all()
                ]);
                break;
                
            case 'POST':
                $request = (object) json_decode(file_get_contents("php://input"), true);

                $validator = new Validator;
                $validator->addValidator('unique', new UniqueRule($this->pdo));

                $rules = [
                    'email' => 'required|unique:users,email',
                    'name' => 'required',
                    'phone' => 'required|numeric',
                    'role_id' => 'required|numeric',
                ];

                $validation = $validator->validate((array) $request, $rules);

                if ($validation->fails()) {
                    // handling errors
                    http_response_code(422);

                    echo json_encode([
                        'status' => false,
                        'message' => 'Errors found',
                        'data' => $validation->errors()->firstOfAll()
                    ]);

                    break;
                }
                
                // validation passes
                $user = new User($this->pdo);
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->role_id = $request->role_id;
                $user->created_at = date('Y-m-d h:i:s');
                $user->updated_at = date('Y-m-d h:i:s');
                
                $id = $user->insert();
                
                http_response_code(201);
                echo json_encode([
                    'status' => true,
                    'message' => 'User created',
                    'data' => $user->retrieve()->where('id', $id)->get()
                ]);
                
                break;
            default:
                http_response_code(405);
                header('Allow: GET, POST');
        }
    }

}