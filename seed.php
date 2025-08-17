<?php
// seed.php
require_once 'database.php';

// Since we deleted the db file, this will recreate it and the tables
$db = new Database();
$pdo = $db->getConnection();

echo "Seeding products...\n";
$products = [
    // Dry Sweets & Snacks (IDs 1-4)
    ['Besan Ladoo', 'Classic sweet balls made from gram flour, sugar, and ghee.', 'sweets_snacks', 15.00, 'images/besan_ladoo.jpg'],
    ['Shankarpali', 'Crispy, sweet, and diamond-shaped fried cookies.', 'sweets_snacks', 12.50, 'images/shankarpali.jpg'],
    ['Chakli', 'A spiral-shaped, savory, and crunchy snack.', 'sweets_snacks', 10.00, 'images/chakli.jpg'],
    ['Poha Chivda', 'A savory snack made from flattened rice, nuts, and spices.', 'sweets_snacks', 11.00, 'images/poha_chivda.jpg'],

    // Made to Order (IDs 5-7)
    ['Puran Poli', 'Sweet flatbread stuffed with a lentil and jaggery filling.', 'made_to_order', 20.00, 'images/puran_poli.jpg'],
    ['Modak', 'Sweet rice flour dumplings filled with a coconut and jaggery mixture.', 'made_to_order', 25.00, 'images/modak.jpg'],
    ['Thalipeeth', 'A savory multi-grain flatbread.', 'made_to_order', 18.00, 'images/thalipeeth.jpg']
];

$stmt = $pdo->prepare('INSERT INTO products (name, description, category, price, image_url) VALUES (?, ?, ?, ?, ?)');
foreach ($products as $product) {
    $stmt->execute($product);
}
echo "Seeded " . count($products) . " products.\n";


echo "Seeding plans...\n";
$plans = [
    ['Festival Feast', 'A grand selection of sweets and savory items perfect for festive occasions.', 35.50],
    ['Small Party Pack', 'A curated pack of our most popular snacks for small gatherings.', 18.00]
];

$plan_stmt = $pdo->prepare('INSERT INTO plans (name, description, price_per_person) VALUES (?, ?, ?)');
foreach ($plans as $plan) {
    $plan_stmt->execute($plan);
}
echo "Seeded " . count($plans) . " plans.\n";

// Plan 1: Festival Feast (ID 1) -> Puran Poli (5), Modak (6), Besan Ladoo (1), Chakli (3)
// Plan 2: Small Party Pack (ID 2) -> Shankarpali (2), Poha Chivda (4), Chakli (3)
$plan_items = [
    // Festival Feast
    [1, 5],
    [1, 6],
    [1, 1],
    [1, 3],
    // Small Party Pack
    [2, 2],
    [2, 4],
    [2, 3],
];

$plan_items_stmt = $pdo->prepare('INSERT INTO plan_items (plan_id, product_id) VALUES (?, ?)');
foreach ($plan_items as $item) {
    $plan_items_stmt->execute($item);
}
echo "Seeded " . count($plan_items) . " plan items.\n";

echo "Database seeding complete.\n";
?>
