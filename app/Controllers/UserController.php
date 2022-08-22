<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\UserShift;
use Rakit\Validation\Validator;
use App\Utils\Rules\{UniqueRule, ExistRule};
use PDO;

class UserController extends BaseController
{
    public function __construct(private PDO $pdo)
    {
    }
    
    public function processRequest(string $method, ?string $id, ?string $shift): void
    {
        if ($id) {
            
            $this->processResourceRequest($method, $id, $shift);
            
        } else {
            
            $this->processCollectionRequest($method);
            
        }
    }
    
    private function processResourceRequest(string $method, string $id, string $path): void
    {
        switch ($method) {
            case 'GET':
                if (isset($path) && $path == 'shift') {
                    $sql = "SELECT users.name, users.email, users.phone, roles.name as role_name, shifts.period, user_shift.shift_date";
                    $sql .= " FROM users";
                    $sql .= " JOIN roles ON `users`.`role_id` = `roles`.`id`";
                    $sql .= " JOIN user_shift ON users.id = user_shift.user_id";
                    $sql .= " JOIN shifts ON shifts.id = user_shift.shift_id";
                    $sql .= " WHERE shifts.id = {$id}";

                    $stmt = $this->pdo->prepare($sql);
                    $stmt->setFetchMode(PDO::FETCH_CLASS, User::class, [$this->pdo]);
                    $stmt->execute();

                    echo json_encode([
                        'status' => true,
                        'message' => 'User Shift fetched Successfully',
                        'data' => $stmt->fetchAll()
                    ]);
                    
                } else {
                    $user = new User($this->pdo);

                    echo json_encode([
                        'status' => true,
                        'message' => 'User fetched Successfully',
                        'data' => $user->retrieve()->where('id', $id)->get()
                    ]);
                }

                break;

            case 'POST':
                if ($path == 'shift') {
                    $conn = $this->pdo;
                    $request = (object) json_decode(file_get_contents("php://input"), true);
                    $validator = new Validator;
                    $validator->addValidator('exists', new ExistRule($conn));

                    $rules = [
                        'shift_id' => 'required|numeric|exists:shifts,id',
                        'shift_date' => [
                            'required', 'date',
                            function ($value) use($conn, $id) {
                                $userShift = new UserShift($conn);
                                $shiftExist = $userShift->retrieve(["COUNT('*') as total"])->where('user_id', "$id")
                                ->andWhere('shift_date', $value)
                                ->limit(1)->get();

                                // false = invalid
                                if (is_numeric($shiftExist->total) AND $shiftExist->total == 1) {
                                    return ":attribute has been clocked in for user.";
                                }
                            }
                        ]
                    ];
                }

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
                $userShift = new UserShift($this->pdo);
                $userShift->user_id = $id;
                $userShift->shift_id = $request->shift_id;
                $userShift->shift_date = $request->shift_date;
                
                $id = $userShift->insert();
                
                http_response_code(201);
                echo json_encode([
                    'status' => true,
                    'message' => 'User Shift created',
                    'data' => $userShift->retrieve()->where('id', $id)->get()
                ]);
                
                break;
            
            default:
                http_response_code(405);
                header('Allow: GET, POST');
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