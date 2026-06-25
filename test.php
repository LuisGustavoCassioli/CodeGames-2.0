<?php
require __DIR__ . '/src/Config/Database.php';
$db = App\Config\Database::getConnection();
$stmt = $db->query('SELECT slug, title FROM products');
print_r($stmt->fetchAll());
