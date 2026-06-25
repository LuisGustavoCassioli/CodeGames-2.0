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

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
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
    $id = $game['id'];
    $title = $game['nome'];
    // basic slugify
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $description = $game['sinopse'] ?? '';
    
    // Parse price
    $valorRaw = $game['valor'] ?? '';
    $price = 0.00;
    if (strpos(strtolower($valorRaw), 'gratuito') !== false) {
        $price = 0.00;
    } else {
        // Remove "R$ ", replace "," with "."
        $clean = str_replace(['R$', ' ', '.'], '', $valorRaw); // remove R$, spaces, and thousand dots
        $clean = str_replace(',', '.', $clean);
        $price = (float) $clean;
    }
    
    $image_url = $game['imagem'] ?? '';
    // Fix relative assets if necessary
    if (strpos($image_url, 'ASSETS/') === 0) {
        $image_url = '/' . $image_url;
    }
    
    $stock = 100;
    $platform = 'Digital'; // Default

    try {
        $stmt->execute([
            ':id' => $id + 1000, // Offset IDs to avoid conflict with seeded games
            ':title' => $title,
            ':slug' => $slug,
            ':description' => $description,
            ':price' => $price,
            ':image_url' => $image_url,
            ':stock' => $stock,
            ':platform' => $platform
        ]);
        $count++;
    } catch (PDOException $e) {
        echo "Erro inserindo $title: " . $e->getMessage() . "\n";
    }
}

echo "Migração concluída! $count jogos importados para o CodeGames 2.0!\n";
