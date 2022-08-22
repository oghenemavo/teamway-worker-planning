<?php

namespace App\Models;

use PDO;

class BaseModel
{
    protected array $cols = [];

    public string $table;

    public function __construct(private PDO $pdo)
    {
    }

    public function __get($name)
    {
        return $this->{$name};
    }
    
    public function __set($name, $value)
    {
        $this->cols[] = $name;
        $this->{$name} = $value;
    }

    public function insert() {
        $tableColumns = implode(',', $this->cols);
        $placeholder = implode(',', array_map(fn($item) => ':' . $item, $this->cols));
        $data = [];

        foreach ($this->cols as $value) {
            $data[$value] = $this->{$value};
        }

        $sql = "INSERT INTO {$this->table}";
        $sql .= " ({$tableColumns}) ";
        $sql .= " VALUES ({$placeholder})";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->execute($data);
            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function read()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} ORDER BY id DESC");
            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class(), [$this->pdo]);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function delete() {
        try {
            foreach ($this->ids as $id) {
                $sql = "DELETE FROM {$this->table} WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['id' => $id]);
            }
            
            if ($stmt->rowCount()) {
                return [
                    'status' => true,
                    'message' => 'Product Deleted Successfully'
                ];
            }
        } catch (\PDOException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}