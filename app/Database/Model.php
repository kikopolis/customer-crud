<?php
declare(strict_types = 1);

namespace Kikopolis\CustomerCrud\Database;

use JsonSerializable;
use PDO;
use RuntimeException;

abstract class Model implements JsonSerializable {
    const EXISTING_RECORD = [
        'id' => 'missing',
    ];
    protected string $table;
    protected array $errors = [];
    protected array $attributes = [];
    protected array $hidden = [];
    protected array $oldAttributes = [];
    
    public function __construct(array $attributes = []) {
        $this->attributes = $attributes;
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
    
    public function getTable(): string {
        if ($this->table) {
            return $this->table;
        }
        $className = get_class($this);
        $className = explode('\\', $className);
        $className = strtolower(end($className));
        return str_replace('model', '', $className);
    }
    
    /**
     * @return array<Model>
     */
    public static function all(): array {
        $model = new static();
        $sql = "select * from {$model->getTable()}";
        $connection = DB::getInstance()->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $models = [];
        foreach ($data as $attrs) {
            $model = new static();
            $model->fill($attrs);
            $models[] = $model;
        }
        return $models;
    }
    
    public static function find(int|string $id): Model {
        $model = new static();
        $sql = "select * from {$model->getTable()} where id = :id";
        $connection = DB::getInstance()->getConnection();
        $stmt = $connection->prepare($sql);
        if (is_int($id)) {
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        }
        $stmt->execute();
        $attrs = $stmt->fetch(PDO::FETCH_ASSOC);
        $model->fill($attrs);
        return $model;
    }
    
    public static function findBy(array $conditions): array {
        $model = new static();
        $sql = "select * from {$model->getTable()} where ";
        $columns = array_keys($conditions);
        array_walk($columns, fn(&$column) => $column = "$column = :$column");
        $columns = implode(' and ', $columns);
        $sql .= $columns;
        $connection = DB::getInstance()->getConnection();
        $stmt = $connection->prepare($sql);
        foreach ($conditions as $key => $value) {
            $type = match (gettype($value)) {
                'integer' => PDO::PARAM_INT,
                'boolean' => PDO::PARAM_BOOL,
                'NULL'    => PDO::PARAM_NULL,
                default   => PDO::PARAM_STR,
            };
            $stmt->bindValue(":$key", $value, $type);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $models = [];
        foreach ($data as $attrs) {
            $model = new static();
            $model->fill($attrs);
            $models[] = $model;
        }
        return $models;
    }
    
    public static function insert(array $data): bool {
        $model = new static($data);
        return $model->save();
    }
    
    public static function update(array $data): bool {
        $model = static::find($data['id']);
        $model->fill($data);
        return $model->sync();
    }
    
    public static function delete(int|string $id): bool {
        $model = new static();
        $sql = "delete from {$model->getTable()} where id = :id";
        $connection = DB::getInstance()->getConnection();
        $stmt = $connection->prepare($sql);
        if (is_int($id)) {
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        }
        return $stmt->execute();
    }
    
    public static function count(): int {
        $model = new static();
        $sql = "select count(*) from {$model->getTable()}";
        $connection = DB::getInstance()->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
    
    public function save(): bool {
        $this->validate($this->attributes, self::EXISTING_RECORD);
        if (!empty($this->errors)) {
            throw new RuntimeException(implode(', ', $this->errors));
        }
        $this->hashPassword();
        $sql = "insert into {$this->getTable()} ";
        $columns = array_keys($this->attributes);
        array_walk($columns, fn(&$column) => $column = "$column");
        $placeholders = implode(', ', array_map(fn($column) => ":$column", $columns));
        $columns = implode(', ', $columns);
        $sql .= "($columns) values ($placeholders)";
        $connection = DB::getInstance()->getConnection();
        $stmt = $connection->prepare($sql);
        foreach ($this->attributes as $key => $value) {
            $type = match (gettype($value)) {
                'integer' => PDO::PARAM_INT,
                'boolean' => PDO::PARAM_BOOL,
                'NULL'    => PDO::PARAM_NULL,
                default   => PDO::PARAM_STR,
            };
            $stmt->bindValue(":$key", $value, $type);
        }
        return $stmt->execute();
    }
    
    public function sync(): bool {
        $this->validate($this->attributes, []);
        if (!empty($this->errors)) {
            throw new RuntimeException(implode(', ', $this->errors));
        }
        $difference = array_diff_assoc($this->attributes, $this->oldAttributes);
        if (empty($difference)) {
            return true;
        }
        $this->hashPassword();
        $sql = "update {$this->getTable()} ";
        $id = $this->attributes['id'];
        unset($this->attributes['id']);
        $columns = array_keys($difference);
        array_walk($columns, fn(&$column) => $column = "$column = :$column");
        $columns = implode(', ', $columns);
        $sql .= "set $columns where id = :id";
        $connection = DB::getInstance()->getConnection();
        $stmt = $connection->prepare($sql);
        foreach ($difference as $key => $value) {
            $type = match (gettype($value)) {
                'integer' => PDO::PARAM_INT,
                'boolean' => PDO::PARAM_BOOL,
                'NULL'    => PDO::PARAM_NULL,
                default   => PDO::PARAM_STR,
            };
            $stmt->bindValue(":$key", $value, $type);
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function getInsertedId(): int|string {
        $connection = DB::getInstance()->getConnection();
        return (int) $connection->lastInsertId();
    }
    
    public function __get(string $name): mixed {
        if (isset($this->attributes[$name]) && !in_array($name, $this->hidden)) {
            return $this->attributes[$name];
        }
        return null;
    }
    
    public function __set(string $name, mixed $value): void {
        $this->attributes[$name] = $value;
    }
    
    public function jsonSerialize(): array {
        $attributes = [];
        foreach ($this->attributes as $key => $value) {
            if (in_array($key, $this->hidden)) {
                continue;
            }
            $attributes[$key] = $value;
        }
        return $attributes;
    }
    
    protected function fill(array $data): void {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->hidden)) {
                continue;
            }
            $this->attributes[$key] = $value;
        }
        $this->oldAttributes = $this->attributes;
    }
    
    protected function validate(array $data, array $rules): void {
        $rules = array_merge($this->rules, $rules);
        $parsedRules = [];
        foreach ($rules as $key => $rule) {
            if (str_contains($rule, '|')) {
                $parsedRules[] = ['key' => $key, 'rules' => explode('|', $rule)];
            } else {
                $parsedRules[] = ['key' => $key, 'rules' => [$rule]];
            }
            
        }
        foreach ($parsedRules as $parsedRule) {
            foreach ($parsedRule['rules'] as $rule) {
                $this->validateRule($parsedRule['key'], $data[$parsedRule['key']] ?? null, $rule);
            }
        }
    }
    
    /**
     * @return void
     */
    public function hashPassword(): void {
        if (isset($this->attributes['password'])) {
            $this->attributes['password'] = password_hash($this->attributes['password'], PASSWORD_DEFAULT);
        }
    }
    
    private function validateRule(int|string $key, mixed $value, string|array $rule): void {
        if (is_array($rule)) {
            foreach ($rule as $subRule) {
                $this->validateRule($key, $value, $subRule);
            }
        } else {
            switch ($rule) {
                case 'missing':
                    if ($value) {
                        $this->errors[$key] = "$key cannot be defined";
                    }
                    break;
                case 'required':
                    if (!$value) {
                        $this->errors[$key] = "$key is required";
                    }
                    break;
                case 'string':
                    if (!is_string($value)) {
                        $this->errors[$key] = "$key must be a string";
                    }
                    break;
                case 'integer':
                    if (!is_int($value)) {
                        $this->errors[$key] = "$key must be an integer";
                    }
                    break;
                case 'date':
                    if (!is_string($value) || !strtotime($value)) {
                        $this->errors[$key] = "$key must be a date";
                    }
                    break;
                case 'unique':
                    $model = static::findBy([$key => $value])[0];
                    if ($model) {
                        $this->errors[$key] = "$key must be unique";
                    }
            }
        }
    }
}
