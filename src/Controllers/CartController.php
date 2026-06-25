<?php

namespace App\Controllers;

use App\Models\CartModel;

class CartController {
    private CartModel $cartModel;

    public function __construct() {
        $this->cartModel = new CartModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart_session'])) {
            $_SESSION['cart_session'] = bin2hex(random_bytes(16));
        }
    }

    public function index() {
        $items = $this->cartModel->getItems($_SESSION['cart_session'], $_SESSION['user_id'] ?? null);
        
        $coupon = $_SESSION['coupon'] ?? null;
        $totals = $this->cartModel->getTotals($_SESSION['cart_session'], $_SESSION['user_id'] ?? null, $coupon);

        view('checkout/cart', [
            'items' => $items,
            'totals' => $totals,
            'coupon' => $coupon
        ]);
    }

    public function add() {
        $productId = $_POST['product_id'] ?? null;
        if ($productId) {
            $this->cartModel->addItem($_SESSION['cart_session'], $_SESSION['user_id'] ?? null, $productId);
            $_SESSION['success'] = 'Produto adicionado ao carrinho.';
        }
        header('Location: /cart');
        exit;
    }

    public function remove() {
        $itemId = $_POST['item_id'] ?? null;
        if ($itemId) {
            $this->cartModel->removeItem($itemId);
            $_SESSION['success'] = 'Produto removido do carrinho.';
        }
        header('Location: /cart');
        exit;
    }

    public function update() {
        $itemId = $_POST['item_id'] ?? null;
        $action = $_POST['action'] ?? null; // 'increase' ou 'decrease'
        $currentQuantity = (int)($_POST['current_quantity'] ?? 1);

        if ($itemId && $action) {
            $newQuantity = $action === 'increase' ? $currentQuantity + 1 : $currentQuantity - 1;
            
            if ($newQuantity > 0) {
                $this->cartModel->updateItemQuantity($itemId, $newQuantity);
            } else {
                $this->cartModel->removeItem($itemId);
                $_SESSION['success'] = 'Produto removido do carrinho.';
            }
        }
        header('Location: /cart');
        exit;
    }

    public function applyCoupon() {
        $code = $_POST['coupon_code'] ?? '';
        if (empty($code)) {
            $_SESSION['error'] = 'Por favor, insira um código de cupom.';
            header('Location: /cart');
            exit;
        }

        $couponModel = new \App\Models\CouponModel();
        $coupon = $couponModel->findByCode(strtoupper($code));

        if (!$coupon) {
            $_SESSION['error'] = 'Cupom inválido.';
            header('Location: /cart');
            exit;
        }

        if (!$couponModel->isValid($coupon)) {
            $_SESSION['error'] = 'Cupom expirado ou limite de uso excedido.';
            header('Location: /cart');
            exit;
        }

        $_SESSION['coupon'] = $coupon;
        $_SESSION['success'] = 'Cupom aplicado com sucesso!';
        header('Location: /cart');
        exit;
    }

    public function removeCoupon() {
        unset($_SESSION['coupon']);
        $_SESSION['success'] = 'Cupom removido com sucesso.';
        header('Location: /cart');
        exit;
    }
}
