<?php
try {
    $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
    $stmt = $db->query('SELECT COUNT(*) FROM products');
    echo "SQLite Products: " . $stmt->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
