<?php
declare(strict_types = 1);

namespace Kikopolis\CustomerCrud\Database;

use PDO;
use RuntimeException;

class DB {
    private static ?DB $instance = null;
    private ?PDO $pdo = null;
    private ?string $host = null;
    private ?string $port = null;
    private ?string $user = null;
    private ?string $password = null;
    private ?string $database = null;
    private ?string $charset = null;
    
    public static function getInstance(array $options = [], bool $forceNew = false): DB {
        if (self::$instance === null || $forceNew) {
            self::$instance = new DB();
            self::$instance->initOptions($options);
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s;port=%s;', self::$instance->host, self::$instance->database, self::$instance->charset, self::$instance->port);
            $pdoOptions = [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION];
            self::$instance->pdo = new PDO($dsn, self::$instance->user, self::$instance->password, $pdoOptions);
        }
        return self::$instance;
    }
    
    public function getConnection(): PDO {
        if (!$this->pdo) {
            throw new RuntimeException('DB connection is undefined');
        }
        return $this->pdo;
    }
    
    private function initOptions(array $options): void {
        if (isset($options['host'])) {
            $this->host = $options['host'];
        } else {
            $this->host = env('DB_HOST');
        }
        if (isset($options['port'])) {
            $this->port = $options['port'];
        } else {
            $this->port = env('DB_PORT');
        }
        if (isset($options['user'])) {
            $this->user = $options['user'];
        } else {
            $this->user = env('DB_USER');
        }
        if (isset($options['password'])) {
            $this->password = $options['password'];
        } else {
            $this->password = env('DB_PASSWORD');
        }
        if (isset($options['database'])) {
            $this->database = $options['database'];
        } else {
            $this->database = env('DB_DATABASE');
        }
        if (isset($options['charset'])) {
            $this->charset = $options['charset'];
        } else {
            $this->charset = env('DB_CHARSET', 'utf8mb4');
        }
    }
}
