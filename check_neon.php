<?php
try {
    $dbUrl = "postgresql://neondb_owner:npg_67IPSsfmzcwa@ep-spring-haze-acvf9pfu-pooler.sa-east-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require";
    $dbopts = parse_url($dbUrl);
    $port = $dbopts["port"] ?? 5432;
    $dsn = "pgsql:host={$dbopts["host"]};port={$port};dbname=" . ltrim($dbopts["path"], '/');
    $user = $dbopts["user"] ?? null;
    $pass = $dbopts["pass"] ?? null;

    $db = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $stmt = $db->query('SELECT COUNT(*) FROM products');
    echo "Neon Postgres Products: " . $stmt->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
