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

// Set the page title for the header
$page_title = $category_name . ' - ' . trans('business_name');

$stmt = $pdo->prepare('SELECT * FROM products WHERE category = ?');
$stmt->execute([$category]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'header.php';
?>

<section id="menu-page">
    <h2><?php echo htmlspecialchars($category_name); ?></h2>

    <div class="product-grid">
        <?php if (empty($products)): ?>
            <p><?php echo trans('no_products_found'); ?></p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p><strong><?php echo trans('price_label'); ?>:</strong> $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                    <form action="cart_actions.php" method="POST" class="add-to-cart-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" class="btn"><?php echo trans('add_to_cart'); ?></button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'footer.php'; ?>
