<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class OrderModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function createOrder(string $userId, array $cartItems, float $totalAmount, string $paymentMethod, string $status = 'COMPLETED'): ?string {
        try {
            $this->db->beginTransaction();

            // 1. Cria o Order
            $orderId = Database::generateUuid();
            $stmt = $this->db->prepare("INSERT INTO orders (id, user_id, total_amount, payment_method, status) VALUES (:id, :user_id, :total, :method, :status)");
            $stmt->execute([
                'id' => $orderId,
                'user_id' => $userId,
                'total' => $totalAmount,
                'method' => $paymentMethod,
                'status' => $status
            ]);

            // 2. Insere Order Items e diminui o estoque
            foreach ($cartItems as $item) {
                // Generate a random game key
                $gameKey = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5) . '-' . substr(bin2hex(random_bytes(3)), 0, 5) . '-' . substr(bin2hex(random_bytes(3)), 0, 5));

                // Item
                $itemId = Database::generateUuid();
                $itemStmt = $this->db->prepare("INSERT INTO order_items (id, order_id, product_id, quantity, price_at_time, game_key) VALUES (:id, :order_id, :product_id, :qty, :price, :game_key)");
                $itemStmt->execute([
                    'id' => $itemId,
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'qty' => $item['quantity'],
                    'price' => $item['price'],
                    'game_key' => $gameKey
                ]);

                // Estoque (ignora validação estrita no mock)
                $stockStmt = $this->db->prepare("UPDATE products SET stock = stock - :qty WHERE id = :product_id AND stock >= :qty");
                $stockStmt->execute([
                    'qty' => $item['quantity'],
                    'product_id' => $item['product_id']
                ]);
            }

            // 3. Limpa carrinho (será feito no controller)

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return null;
        }
    }

    public function updateOrderStatus(string $orderId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE orders SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute([
            'status' => $status,
            'id' => $orderId
        ]);
    }

    public function getOrderById(string $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch();
        if (!$order) return null;

        $itemsStmt = $this->db->prepare("
            SELECT oi.*, p.title, p.image_url 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $itemsStmt->execute(['order_id' => $order['id']]);
        $order['items'] = $itemsStmt->fetchAll();

        return $order;
    }

    public function getOrdersByUser(string $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        $orders = $stmt->fetchAll();

        foreach ($orders as &$order) {
            $itemsStmt = $this->db->prepare("
                SELECT oi.*, p.title, p.image_url 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id
            ");
            $itemsStmt->execute(['order_id' => $order['id']]);
            $order['items'] = $itemsStmt->fetchAll();
        }

        return $orders;
    }
}
