<?php

// Autoloader super simples para carregar classes de 'src/' mapeadas para 'App\'
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

session_start();

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Helper para renderizar view
function view($name, $data = []) {
    extract($data);
    $path = __DIR__ . '/../views/' . $name . '.php';
    if (file_exists($path)) {
        require __DIR__ . '/../views/layouts/header.php';
        require $path;
        require __DIR__ . '/../views/layouts/footer.php';
    } else {
        http_response_code(404);
        echo "View $name não encontrada.";
    }
}

// Router Simples
switch ($requestUri) {
    case '/':
        (new App\Controllers\ProductController())->index();
        break;
        
    case '/product':
        (new App\Controllers\ProductController())->show($_GET['slug'] ?? '');
        break;

    case '/login':
        if ($method === 'POST') {
            (new App\Controllers\AuthController())->processLogin();
        } else {
            (new App\Controllers\AuthController())->loginForm();
        }
        break;

    case '/register':
        if ($method === 'POST') {
            (new App\Controllers\AuthController())->processRegister();
        } else {
            (new App\Controllers\AuthController())->registerForm();
        }
        break;

    case '/logout':
        (new App\Controllers\AuthController())->logout();
        break;

    case '/cart':
        if ($method === 'POST') {
            (new App\Controllers\CartController())->add();
        } else {
            (new App\Controllers\CartController())->index();
        }
        break;
        
    case '/cart/remove':
        if ($method === 'POST') {
            (new App\Controllers\CartController())->remove();
        }
        break;

    case '/cart/update':
        if ($method === 'POST') {
            (new App\Controllers\CartController())->update();
        }
        break;

    case '/checkout':
        if ($method === 'POST') {
            (new App\Controllers\OrderController())->processCheckout();
        } else {
            (new App\Controllers\OrderController())->checkoutForm();
        }
        break;

    case '/checkout/pix':
        (new App\Controllers\OrderController())->pixPayment($_GET['id'] ?? '');
        break;

    case '/checkout/pix/process':
        if ($method === 'POST') {
            (new App\Controllers\OrderController())->processPixPayment($_GET['id'] ?? '');
        }
        break;

    case '/orders':
        (new App\Controllers\OrderController())->myOrders();
        break;

    case '/admin/products':
        (new App\Controllers\AdminController())->index();
        break;
        
    case '/admin/products/create':
        (new App\Controllers\AdminController())->create();
        break;
        
    case '/admin/products/store':
        (new App\Controllers\AdminController())->store();
        break;
        
    case '/admin/products/edit':
        (new App\Controllers\AdminController())->edit($_GET['id'] ?? '');
        break;
        
    case '/admin/products/update':
        (new App\Controllers\AdminController())->update($_GET['id'] ?? '');
        break;
        
    case '/admin/products/delete':
        (new App\Controllers\AdminController())->delete($_GET['id'] ?? '');
        break;

    case '/admin/coupons':
        (new App\Controllers\AdminController())->couponsIndex();
        break;
        
    case '/admin/coupons/create':
        (new App\Controllers\AdminController())->couponsCreate();
        break;
        
    case '/admin/coupons/store':
        (new App\Controllers\AdminController())->couponsStore();
        break;
        
    case '/admin/coupons/edit':
        (new App\Controllers\AdminController())->couponsEdit($_GET['id'] ?? '');
        break;
        
    case '/admin/coupons/update':
        (new App\Controllers\AdminController())->couponsUpdate($_GET['id'] ?? '');
        break;
        
    case '/admin/coupons/delete':
        (new App\Controllers\AdminController())->couponsDelete($_GET['id'] ?? '');
        break;

    case '/api/search':
        header('Content-Type: application/json');
        $q = $_GET['q'] ?? '';
        if (strlen($q) < 2) {
            echo json_encode([]);
            exit;
        }
        $products = (new App\Models\ProductModel())->findAll($q);
        echo json_encode($products);
        break;

    case '/cart/apply-coupon':
        if ($method === 'POST') {
            (new App\Controllers\CartController())->applyCoupon();
        }
        break;

    case '/cart/remove-coupon':
        if ($method === 'POST') {
            (new App\Controllers\CartController())->removeCoupon();
        }
        break;

    default:
        http_response_code(404);
        view('404');
        break;
}
