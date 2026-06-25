<?php

namespace App\Controllers;

use App\Models\ProductModel;

class AdminController {
    private ProductModel $productModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->middleware();
        $this->productModel = new ProductModel();
    }

    private function middleware(): void {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
            http_response_code(403);
            die("Acesso Negado. Você não tem permissão para acessar esta área.");
        }
    }

    public function index(): void {
        $products = $this->productModel->findAll();
        require_once __DIR__ . '/../../views/admin/products/index.php';
    }

    public function create(): void {
        $product = null; // Used by the form to know it's a create action
        require_once __DIR__ . '/../../views/admin/products/form.php';
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'] ?? '',
                'slug' => $_POST['slug'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => (float)($_POST['price'] ?? 0),
                'original_price' => !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null,
                'stock' => (int)($_POST['stock'] ?? 0),
                'image_url' => $_POST['image_url'] ?? '',
                'platform' => $_POST['platform'] ?? 'Steam'
            ];

            // Basic slugification if empty
            if (empty($data['slug'])) {
                $data['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['title'])));
            }

            $this->productModel->create($data);
            header("Location: /admin/products");
            exit;
        }
    }

    public function edit(string $id): void {
        $product = $this->productModel->findById($id);
        if (!$product) {
            http_response_code(404);
            die("Produto não encontrado.");
        }
        require_once __DIR__ . '/../../views/admin/products/form.php';
    }

    public function update(string $id): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'] ?? '',
                'slug' => $_POST['slug'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => (float)($_POST['price'] ?? 0),
                'original_price' => !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null,
                'stock' => (int)($_POST['stock'] ?? 0),
                'image_url' => $_POST['image_url'] ?? '',
                'platform' => $_POST['platform'] ?? 'Steam'
            ];

            if (empty($data['slug'])) {
                $data['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['title'])));
            }

            $this->productModel->update($id, $data);
            header("Location: /admin/products");
            exit;
        }
    }

    public function delete(string $id): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->productModel->delete($id);
            header("Location: /admin/products");
            exit;
        }
    }

    // Coupon Methods
    public function couponsIndex(): void {
        $couponModel = new \App\Models\CouponModel();
        $coupons = $couponModel->findAll();
        require_once __DIR__ . '/../../views/admin/coupons/index.php';
    }

    public function couponsCreate(): void {
        $coupon = null;
        require_once __DIR__ . '/../../views/admin/coupons/form.php';
    }

    public function couponsStore(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'] ?? '',
                'discount_type' => $_POST['discount_type'] ?? 'FIXED',
                'discount_value' => (float)($_POST['discount_value'] ?? 0),
                'valid_until' => !empty($_POST['valid_until']) ? $_POST['valid_until'] : null,
                'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null
            ];

            $couponModel = new \App\Models\CouponModel();
            $couponModel->create($data);
            header("Location: /admin/coupons");
            exit;
        }
    }

    public function couponsEdit(string $id): void {
        $couponModel = new \App\Models\CouponModel();
        $coupon = $couponModel->findById($id);
        if (!$coupon) {
            http_response_code(404);
            die("Cupom não encontrado.");
        }
        require_once __DIR__ . '/../../views/admin/coupons/form.php';
    }

    public function couponsUpdate(string $id): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'] ?? '',
                'discount_type' => $_POST['discount_type'] ?? 'FIXED',
                'discount_value' => (float)($_POST['discount_value'] ?? 0),
                'valid_until' => !empty($_POST['valid_until']) ? $_POST['valid_until'] : null,
                'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null
            ];

            $couponModel = new \App\Models\CouponModel();
            $couponModel->update($id, $data);
            header("Location: /admin/coupons");
            exit;
        }
    }

    public function couponsDelete(string $id): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $couponModel = new \App\Models\CouponModel();
            $couponModel->delete($id);
            header("Location: /admin/coupons");
            exit;
        }
    }
}
