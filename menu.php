<?php
require_once 'init.php';

$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Map category to translation key
$category_trans_key = 'menu_page_title'; // Default
if ($category === 'sweets_snacks') {
    $category_trans_key = "nav_sweets_snacks";
} elseif ($category === 'made_to_order') {
    $category_trans_key = "nav_made_to_order";
}
$category_name = trans($category_trans_key);

$stmt = $pdo->prepare('SELECT * FROM products WHERE category = ?');
$stmt->execute([$category]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $category_name; ?> - <?php echo trans('business_name'); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1><a href="index.php" style="color: #fff; text-decoration: none;"><?php echo trans('business_name'); ?></a></h1>
            </div>
            <ul class="nav-links">
                <li><a href="menu.php?category=sweets_snacks"><?php echo trans('nav_sweets_snacks'); ?></a></li>
                <li><a href="menu.php?category=made_to_order"><?php echo trans('nav_made_to_order'); ?></a></li>
                <li><a href="catering.php"><?php echo trans('nav_catering'); ?></a></li>
            </ul>
            <div class="language-selector">
                <a href="?lang=en&category=<?php echo $category; ?>">English</a> |
                <a href="?lang=hi&category=<?php echo $category; ?>">हिंदी</a> |
                <a href="?lang=mr&category=<?php echo $category; ?>">मराठी</a>
            </div>
        </nav>
    </header>

    <main>
        <section id="menu-page">
            <h2><?php echo htmlspecialchars($category_name); ?></h2>

            <div class="product-grid">
                <?php if (empty($products)): ?>
                    <p><?php echo trans('no_products_found'); ?></p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <p><strong><?php echo trans('price_label'); ?>:</strong> $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p><?php echo trans('footer_copyright'); ?></p>
    </footer>

    <script src="js/app.js"></script>
</body>
</html>
