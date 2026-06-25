<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\CartModel;

class OrderController {
    private OrderModel $orderModel;
    private CartModel $cartModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->cartModel = new CartModel();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function checkoutForm() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Você precisa estar logado para finalizar a compra.';
            header('Location: /login');
            exit;
        }

        $items = $this->cartModel->getItems($_SESSION['cart_session'], $_SESSION['user_id']);
        if (empty($items)) {
            header('Location: /cart');
            exit;
        }

        $coupon = $_SESSION['coupon'] ?? null;
        $totals = $this->cartModel->getTotals($_SESSION['cart_session'], $_SESSION['user_id'] ?? null, $coupon);

        view('checkout/checkout', [
            'items' => $items,
            'totals' => $totals,
            'total' => $totals['total']
        ]);
    }

    public function processCheckout() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $paymentMethod = $_POST['payment_method'] ?? 'CREDIT_CARD';
        $items = $this->cartModel->getItems($_SESSION['cart_session'], $_SESSION['user_id']);
        
        if (empty($items)) {
            header('Location: /cart');
            exit;
        }

        $coupon = $_SESSION['coupon'] ?? null;
        $totals = $this->cartModel->getTotals($_SESSION['cart_session'], $_SESSION['user_id'] ?? null, $coupon);
        $total = $totals['total'];

        // Se for PIX, status é PENDING. Caso contrário, COMPLETED.
        $status = ($paymentMethod === 'PIX') ? 'PENDING' : 'COMPLETED';

        // Simulando processamento...
        $orderId = $this->orderModel->createOrder($_SESSION['user_id'], $items, $total, $paymentMethod, $status);

        if ($orderId) {
            $this->cartModel->clearCart($_SESSION['cart_session'], $_SESSION['user_id']);
            
            if ($paymentMethod === 'PIX') {
                header("Location: /checkout/pix?id=" . $orderId);
                exit;
            }

            $_SESSION['success'] = 'Pedido concluído com sucesso! Suas chaves foram geradas.';
            header('Location: /orders');
            exit;
        } else {
            $_SESSION['error'] = 'Erro ao processar pedido.';
            header('Location: /checkout');
            exit;
        }
    }

    public function pixPayment(string $id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $order = $this->orderModel->getOrderById($id);
        if (!$order || $order['user_id'] !== $_SESSION['user_id'] || $order['status'] !== 'PENDING') {
            header('Location: /orders');
            exit;
        }

        view('checkout/pix', ['order' => $order]);
    }

    public function processPixPayment(string $id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $order = $this->orderModel->getOrderById($id);
        if (!$order || $order['user_id'] !== $_SESSION['user_id'] || $order['status'] !== 'PENDING') {
            header('Location: /orders');
            exit;
        }

        $this->orderModel->updateOrderStatus($id, 'COMPLETED');
        $_SESSION['success'] = 'Pagamento PIX confirmado com sucesso! Suas chaves foram liberadas.';
        header('Location: /orders');
        exit;
    }

    public function myOrders() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $orders = $this->orderModel->getOrdersByUser($_SESSION['user_id']);
        view('account/orders', ['orders' => $orders]);
    }
}
