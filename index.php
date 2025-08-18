<?php
require_once 'init.php';

// Set the page title for the header
$page_title = trans('business_name');

// Fetch first 3 products for the featured section
$featured_stmt = $pdo->query('SELECT * FROM products LIMIT 3');
$featured_products = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'header.php';
?>

<section id="hero">
    <img src="https://placehold.co/1200x400/f8a11e/4a2e19?text=Pragati+Mahila+Udyog" alt="A spread of delicious Indian food" class="hero-image">
    <div class="hero-text">
        <h2><?php echo trans('hero_welcome'); ?></h2>
        <p><?php echo trans('hero_subtitle'); ?></p>
    </div>
</section>

<section id="about">
    <h3><?php echo trans('about_title'); ?></h3>
    <p>
        <?php echo trans('about_text'); ?>
    </p>
</section>

<section id="featured-products">
    <h3><?php echo trans('featured_products_title'); ?></h3>
    <div class="product-grid">
        <?php foreach($featured_products as $product): ?>
        <div class="product-card">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <form action="cart_actions.php" method="POST" class="add-to-cart-form">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <button type="submit" class="btn"><?php echo trans('add_to_cart'); ?></button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once 'footer.php'; ?>
