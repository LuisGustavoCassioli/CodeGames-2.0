<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class UserModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $name, string $email, string $passwordHash): bool {
        $id = Database::generateUuid();
        $stmt = $this->db->prepare("
            INSERT INTO users (id, name, email, password_hash) 
            VALUES (:id, :name, :email, :password)
        ");
        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash
        ]);
    }
}
