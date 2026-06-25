<?php

namespace App\Models;

use PDO;
use App\Config\Database;

class CouponModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->initTable(); // Ensure table exists for SQLite fallback
    }

    private function initTable(): void {
        // Safe check to create table if using SQLite or for simple initializations
        $query = "CREATE TABLE IF NOT EXISTS coupons (
            id VARCHAR(36) PRIMARY KEY,
            code VARCHAR(50) UNIQUE NOT NULL,
            discount_type VARCHAR(20) NOT NULL,
            discount_value DECIMAL(10, 2) NOT NULL,
            valid_until TIMESTAMP DEFAULT NULL,
            usage_limit INTEGER DEFAULT NULL,
            used_count INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        try {
            $this->db->exec($query);
        } catch (\PDOException $e) {
            // Ignore if table exists or syntax diff in postgres
        }
    }

    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM coupons ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function findById(string $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM coupons WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByCode(string $code): ?array {
        $stmt = $this->db->prepare("SELECT * FROM coupons WHERE code = :code");
        $stmt->execute(['code' => $code]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): void {
        $id = Database::generateUuid();
        $stmt = $this->db->prepare("
            INSERT INTO coupons (id, code, discount_type, discount_value, valid_until, usage_limit, created_at, updated_at) 
            VALUES (:id, :code, :discount_type, :discount_value, :valid_until, :usage_limit, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        
        $stmt->execute([
            'id' => $id,
            'code' => strtoupper($data['code']),
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'valid_until' => !empty($data['valid_until']) ? $data['valid_until'] : null,
            'usage_limit' => !empty($data['usage_limit']) ? (int)$data['usage_limit'] : null,
        ]);
    }

    public function update(string $id, array $data): void {
        $stmt = $this->db->prepare("
            UPDATE coupons 
            SET code = :code, 
                discount_type = :discount_type, 
                discount_value = :discount_value, 
                valid_until = :valid_until, 
                usage_limit = :usage_limit, 
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        
        $stmt->execute([
            'id' => $id,
            'code' => strtoupper($data['code']),
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'valid_until' => !empty($data['valid_until']) ? $data['valid_until'] : null,
            'usage_limit' => !empty($data['usage_limit']) ? (int)$data['usage_limit'] : null,
        ]);
    }

    public function delete(string $id): void {
        $stmt = $this->db->prepare("DELETE FROM coupons WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function incrementUsage(string $id): void {
        $stmt = $this->db->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function isValid(array $coupon): bool {
        // Check valid_until
        if (!empty($coupon['valid_until'])) {
            $validUntil = strtotime($coupon['valid_until']);
            if ($validUntil !== false && $validUntil < time()) {
                return false;
            }
        }
        
        // Check usage limit
        if (!empty($coupon['usage_limit']) && $coupon['used_count'] >= $coupon['usage_limit']) {
            return false;
        }

        return true;
    }
}
