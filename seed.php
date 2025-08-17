<?php
// seed.php
require_once 'database.php';

// Since we deleted the db file, this will recreate it and the tables
$db = new Database();
$pdo = $db->getConnection();

echo "Seeding products...\n";
$products = [
    // Dry Sweets & Snacks (IDs 1-4)
    ['Besan Ladoo', 'Classic sweet balls made from gram flour, sugar, and ghee.', 'sweets_snacks', 15.00, 'https://placehold.co/400x300/f8a11e/4a2e19?text=Besan+Ladoo'],
    ['Shankarpali', 'Crispy, sweet, and diamond-shaped fried cookies.', 'sweets_snacks', 12.50, 'https://placehold.co/400x300/f8a11e/4a2e19?text=Shankarpali'],
    ['Chakli', 'A spiral-shaped, savory, and crunchy snack.', 'sweets_snacks', 10.00, 'https://placehold.co/400x300/f8a11e/4a2e19?text=Chakli'],
    ['Poha Chivda', 'A savory snack made from flattened rice, nuts, and spices.', 'sweets_snacks', 11.00, 'https://placehold.co/400x300/f8a11e/4a2e19?text=Poha+Chivda'],

    // Made to Order (IDs 5-7)
    ['Puran Poli', 'Sweet flatbread stuffed with a lentil and jaggery filling.', 'made_to_order', 20.00, 'https://placehold.co/400x300/f8a11e/4a2e19?text=Puran+Poli'],
    ['Modak', 'Sweet rice flour dumplings filled with a coconut and jaggery mixture.', 'made_to_order', 25.00, 'https://placehold.co/400x300/f8a11e/4a2e19?text=Modak'],
    ['Thalipeeth', 'A savory multi-grain flatbread.', 'made_to_order', 18.00, 'https://placehold.co/400x300/f8a11e/4a2e19?text=Thalipeeth']
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


echo "Seeding admin user...\n";
$admin_user = 'admin';
$admin_pass = password_hash('password', PASSWORD_DEFAULT);

$admin_stmt = $pdo->prepare('INSERT INTO admins (username, password) VALUES (?, ?)');
try {
    $admin_stmt->execute([$admin_user, $admin_pass]);
    echo "Default admin user ('admin'/'password') created.\n";
} catch (PDOException $e) {
    echo "Admin user likely already exists.\n";
}


echo "Database seeding complete.\n";
?>
