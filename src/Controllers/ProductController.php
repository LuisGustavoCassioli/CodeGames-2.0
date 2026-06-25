<?php

namespace App\Controllers;

use App\Models\ProductModel;

class ProductController {
    private ProductModel $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    public function index() {
        $search = $_GET['q'] ?? null;
        $products = $this->productModel->findAll($search);
        view('catalog/home', ['products' => $products, 'search' => $search]);
    }

    public function catalog() {
        $products = $this->productModel->findAll();
        view('catalog/catalog', ['products' => $products]);
    }

    public function show(string $slug) {
        if (empty($slug)) {
            header('Location: /');
            exit;
        }

        $product = $this->productModel->findBySlug($slug);

        if (!$product) {
            http_response_code(404);
            view('404');
            return;
        }

        view('catalog/detail', ['product' => $product]);
    }
}
