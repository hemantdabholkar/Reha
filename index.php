<?php
require_once 'init.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo trans('business_name'); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1><?php echo trans('business_name'); ?></h1>
            </div>
            <ul class="nav-links">
                <li><a href="menu.php?category=sweets_snacks"><?php echo trans('nav_sweets_snacks'); ?></a></li>
                <li><a href="menu.php?category=made_to_order"><?php echo trans('nav_made_to_order'); ?></a></li>
                <li><a href="catering.php"><?php echo trans('nav_catering'); ?></a></li>
            </ul>
            <div class="language-selector">
                <a href="?lang=en">English</a> |
                <a href="?lang=hi">हिंदी</a> |
                <a href="?lang=mr">मराठी</a>
            </div>
        </nav>
    </header>

    <main>
        <section id="hero">
            <h2><?php echo trans('hero_welcome'); ?></h2>
            <p><?php echo trans('hero_subtitle'); ?></p>
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
                <div class="product-card">
                    <h4><?php echo trans('product_besan_ladoo_name'); ?></h4>
                    <p><?php echo trans('product_besan_ladoo_desc'); ?></p>
                </div>
                <div class="product-card">
                    <h4><?php echo trans('product_shankarpali_name'); ?></h4>
                    <p><?php echo trans('product_shankarpali_desc'); ?></p>
                </div>
                <div class="product-card">
                    <h4><?php echo trans('product_chakli_name'); ?></h4>
                    <p><?php echo trans('product_chakli_desc'); ?></p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p><?php echo trans('footer_copyright'); ?></p>
    </footer>

    <script src="js/app.js"></script>
</body>
</html>
