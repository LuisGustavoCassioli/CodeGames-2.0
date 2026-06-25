<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class ProductModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findAll(?string $search = null): array {
        if ($search) {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE title LIKE :search OR platform LIKE :search ORDER BY created_at DESC");
            $stmt->execute(['search' => '%' . $search . '%']);
            return $stmt->fetchAll();
        }
        $stmt = $this->db->query("SELECT * FROM products ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public function findById(string $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public function create(array $data): void {
        $data['id'] = Database::generateUuid();
        
        $stmt = $this->db->prepare("
            INSERT INTO products (id, title, slug, description, price, original_price, stock, image_url, platform)
            VALUES (:id, :title, :slug, :description, :price, :original_price, :stock, :image_url, :platform)
        ");
        
        $stmt->execute([
            'id' => $data['id'],
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'original_price' => $data['original_price'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'image_url' => $data['image_url'] ?? null,
            'platform' => $data['platform'] ?? 'Steam'
        ]);
    }

    public function update(string $id, array $data): void {
        $stmt = $this->db->prepare("
            UPDATE products 
            SET title = :title, 
                slug = :slug, 
                description = :description, 
                price = :price, 
                original_price = :original_price,
                stock = :stock, 
                image_url = :image_url, 
                platform = :platform,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        
        $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'original_price' => $data['original_price'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'image_url' => $data['image_url'] ?? null,
            'platform' => $data['platform'] ?? 'Steam'
        ]);
    }

    public function delete(string $id): void {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
