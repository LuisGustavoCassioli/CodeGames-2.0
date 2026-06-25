<?php
$jsonPath = 'c:/Users/rodri/.gemini/antigravity/scratch/CodeGamesOld/jogos.json';
$dbPath = __DIR__ . '/../database.sqlite';

if (!file_exists($jsonPath)) {
    die("jogos.json não encontrado!\n");
}

$json = file_get_contents($jsonPath);
// Some files might have BOM or different encoding. Remove BOM if present:
$json = preg_replace('/^[\xef\xbb\xbf]+/', '', $json);

$games = json_decode($json, true);

if (!$games) {
    die("Erro ao ler JSON: " . json_last_error_msg() . "\n");
}

require __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, "\"'");
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

try {
    $pdo = Database::getConnection();
} catch (Exception $e) {
    die("Erro BD: " . $e->getMessage() . "\n");
}

$stmt = $pdo->prepare("
    INSERT INTO products (id, title, slug, description, price, image_url, stock, platform, created_at, updated_at) 
    VALUES (:id, :title, :slug, :description, :price, :image_url, :stock, :platform, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
    ON CONFLICT(id) DO UPDATE SET
        title = excluded.title,
        price = excluded.price,
        image_url = excluded.image_url
");

$count = 0;
foreach ($games as $game) {
    $title = $game['nome'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $description = $game['sinopse'] ?? '';
    
    $valorRaw = $game['valor'] ?? '';
    $price = 0.00;
    if (strpos(strtolower($valorRaw), 'gratuito') !== false) {
        $price = 0.00;
    } else {
        $clean = str_replace(['R$', ' ', '.'], '', $valorRaw); 
        $clean = str_replace(',', '.', $clean);
        $price = (float) $clean;
    }
    
    $image_url = $game['imagem'] ?? '';
    if (strpos($image_url, 'ASSETS/') === 0) {
        $image_url = '/' . $image_url;
    }
    
    $stock = 100;
    $platform = 'Digital';

    try {
        // Check if exists by slug
        $stmtCheck = $pdo->prepare("SELECT id FROM products WHERE slug = ?");
        $stmtCheck->execute([$slug]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            // Update
            $stmtUpdate = $pdo->prepare("
                UPDATE products 
                SET title = ?, price = ?, image_url = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmtUpdate->execute([$title, $price, $image_url, $existing['id']]);
        } else {
            // Insert
            $id = Database::generateUuid();
            $stmtInsert = $pdo->prepare("
                INSERT INTO products (id, title, slug, description, price, image_url, stock, platform, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
            ");
            $stmtInsert->execute([$id, $title, $slug, $description, $price, $image_url, $stock, $platform]);
        }
        $count++;
    } catch (PDOException $e) {
        echo "Erro inserindo $title: " . $e->getMessage() . "\n";
    }
}

echo "Migração concluída! $count jogos processados para o CodeGames 2.0!\n";
