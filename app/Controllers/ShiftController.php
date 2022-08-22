<?php

namespace App\Controllers;

use App\Models\Shift;
use Rakit\Validation\Validator;
use App\Utils\Rules\UniqueRule;
use PDO;

class ShiftController extends BaseController
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
                $shift = new Shift($this->pdo);

                echo json_encode([
                    'status' => true,
                    'message' => 'Shift fetched Successfully',
                    'data' => $shift->retrieve()->where('id', $id)->get()
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
                $shift = new Shift($this->pdo);

                echo json_encode([
                    'status' => true,
                    'message' => 'Shifts fetched Successfully',
                    'data' => $shift->all()
                ]);
                break;
                
            case 'POST':
                $request = (object) json_decode(file_get_contents("php://input"), true);

                $validator = new Validator;
                $validator->addValidator('unique', new UniqueRule($this->pdo));

                $rules = [
                    'period' => 'required|unique:shifts,period|in:0-8,8-16,16-24',
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
                $shift = new Shift($this->pdo);
                $shift->period = trim($request->period);
                $id = $shift->insert();
                
                http_response_code(201);
                echo json_encode([
                    'status' => true,
                    'message' => 'Shift created',
                    'data' => $shift->retrieve()->where('id', $id)->get()
                ]);
                
                break;
            default:
                http_response_code(405);
                header('Allow: GET, POST');
        }
    }

}