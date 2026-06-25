<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class CartModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    private function getOrCreateCart(string $sessionId, ?string $userId = null): string {
        // Busca carrinho existente
        $sql = "SELECT id FROM carts WHERE ";
        $params = [];
        if ($userId) {
            $sql .= "user_id = :user_id";
            $params['user_id'] = $userId;
        } else {
            $sql .= "session_id = :session_id AND user_id IS NULL";
            $params['session_id'] = $sessionId;
        }
        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $cart = $stmt->fetch();

        if ($cart) {
            return $cart['id'];
        }

        // Cria novo
        $newId = Database::generateUuid();
        $insertSql = "INSERT INTO carts (id, user_id, session_id) VALUES (:id, :user_id, :session_id)";
        $insertStmt = $this->db->prepare($insertSql);
        $insertStmt->execute([
            'id' => $newId,
            'user_id' => $userId,
            'session_id' => $sessionId
        ]);
        return $newId;
    }

    public function getItems(string $sessionId, ?string $userId = null): array {
        $cartId = $this->getOrCreateCart($sessionId, $userId);

        $sql = "
            SELECT ci.id as item_id, ci.quantity, p.id as product_id, p.title, p.price, p.image_url, p.slug
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.cart_id = :cart_id
            ORDER BY ci.created_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cart_id' => $cartId]);
        
        return $stmt->fetchAll();
    }

    public function getItemCount(string $sessionId, ?string $userId = null): int {
        $cartId = $this->getOrCreateCart($sessionId, $userId);
        $stmt = $this->db->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = :cart_id");
        $stmt->execute(['cart_id' => $cartId]);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    public function addItem(string $sessionId, ?string $userId, string $productId, int $quantity = 1): bool {
        $cartId = $this->getOrCreateCart($sessionId, $userId);

        // Verifica se já existe o item
        $checkStmt = $this->db->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id");
        $checkStmt->execute(['cart_id' => $cartId, 'product_id' => $productId]);
        $existing = $checkStmt->fetch();

        if ($existing) {
            // Atualiza quantidade
            $updStmt = $this->db->prepare("UPDATE cart_items SET quantity = quantity + :qty, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
            return $updStmt->execute(['qty' => $quantity, 'id' => $existing['id']]);
        } else {
            // Insere novo
            $itemId = Database::generateUuid();
            $insStmt = $this->db->prepare("INSERT INTO cart_items (id, cart_id, product_id, quantity) VALUES (:id, :cart_id, :product_id, :qty)");
            return $insStmt->execute([
                'id' => $itemId,
                'cart_id' => $cartId,
                'product_id' => $productId,
                'qty' => $quantity
            ]);
        }
    }

    public function removeItem(string $itemId): bool {
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE id = :id");
        return $stmt->execute(['id' => $itemId]);
    }

    public function updateItemQuantity(string $itemId, int $quantity): bool {
        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }
        $stmt = $this->db->prepare("UPDATE cart_items SET quantity = :qty, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute(['qty' => $quantity, 'id' => $itemId]);
    }

    public function clearCart(string $sessionId, ?string $userId = null): bool {
        $cartId = $this->getOrCreateCart($sessionId, $userId);
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE cart_id = :cart_id");
        return $stmt->execute(['cart_id' => $cartId]);
    }

    public function getTotals(string $sessionId, ?string $userId = null, ?array $coupon = null): array {
        $items = $this->getItems($sessionId, $userId);
        
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }
        
        $discountAmount = 0;
        
        if ($coupon && $subtotal > 0) {
            if ($coupon['discount_type'] === 'PERCENTAGE') {
                $discountAmount = $subtotal * ($coupon['discount_value'] / 100);
            } elseif ($coupon['discount_type'] === 'FIXED') {
                $discountAmount = (float)$coupon['discount_value'];
            }
            
            // Prevent discount from being greater than subtotal
            if ($discountAmount > $subtotal) {
                $discountAmount = $subtotal;
            }
        }
        
        $total = $subtotal - $discountAmount;
        
        return [
            'subtotal' => $subtotal,
            'discount' => $discountAmount,
            'total' => $total
        ];
    }
}
