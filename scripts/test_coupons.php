<?php
session_start();
$_SERVER['REQUEST_URI'] = '/admin/coupons';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SESSION['user_id'] = '1';
$_SESSION['user_role'] = 'ADMIN';

ob_start();
require 'public/index.php';
$html = ob_get_clean();

file_put_contents('scripts/coupons_output.html', $html);
echo "Output saved.\n";
