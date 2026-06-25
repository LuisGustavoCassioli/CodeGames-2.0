<?php
require __DIR__ . '/src/Config/Database.php';
$db = \App\Config\Database::getConnection();
$stmt = $db->query("SELECT COUNT(*) FROM products");
echo "Products in DB: " . $stmt->fetchColumn() . "\n";
