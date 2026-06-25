<?php
// seed.php
require __DIR__ . '/src/Config/Database.php';

use App\Config\Database;

$db = Database::getConnection();

function generateUuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

$products = [
    [
        'title' => 'The Legend of Zelda: Tears of the Kingdom',
        'slug' => 'zelda-tears-of-the-kingdom',
        'description' => "Explore the vast land and skies of Hyrule. An epic adventure awaits.",
        'price' => 349.90,
        'image_url' => 'https://assets.nintendo.com/image/upload/ar_16:9,c_lpad,w_1240/b_white/f_auto/q_auto/ncom/software/switch/70010000063714/c42553b4fd0312c31e70ec7468c6c9bccd739f340152925b9600631f273cce23',
        'stock' => 50
    ],
    [
        'title' => 'Elden Ring',
        'slug' => 'elden-ring',
        'description' => "Rise, Tarnished, and be guided by grace to brandish the power of the Elden Ring.",
        'price' => 249.90,
        'image_url' => 'https://image.api.playstation.com/vulcan/ap/rnd/202110/2016/51r26R7QW8gGvw6iE7z4A9wZ.jpg',
        'stock' => 120
    ],
    [
        'title' => 'Baldur\'s Gate 3',
        'slug' => 'baldurs-gate-3',
        'description' => "Gather your party and return to the Forgotten Realms in a tale of fellowship and betrayal.",
        'price' => 199.90,
        'image_url' => 'https://image.api.playstation.com/vulcan/ap/rnd/202302/2321/3098481c9164bb5f33069b37e49fba1a572ea3b89971ee7b.jpg',
        'stock' => 200
    ],
    [
        'title' => 'Hollow Knight',
        'slug' => 'hollow-knight',
        'description' => "Forge your own path in Hollow Knight! An epic action adventure through a vast ruined kingdom of insects and heroes.",
        'price' => 46.99,
        'image_url' => 'https://cdn.akamai.steamstatic.com/steam/apps/367520/capsule_616x353.jpg',
        'stock' => 0 // Out of stock to show UI behavior
    ]
];

foreach ($products as $p) {
    // Check if exists
    $stmt = $db->prepare("SELECT id FROM products WHERE slug = ?");
    $stmt->execute([$p['slug']]);
    if ($stmt->fetch()) {
        continue;
    }

    $p['id'] = generateUuid();

    $insert = $db->prepare("
        INSERT INTO products (id, title, slug, description, price, image_url, stock) 
        VALUES (:id, :title, :slug, :description, :price, :image_url, :stock)
    ");
    $insert->execute($p);
    echo "Inserted: " . $p['title'] . "\n";
}

echo "Seeding completed!\n";
