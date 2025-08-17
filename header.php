<?php
// This file is included at the top of all public-facing pages
// init.php is already included by the page itself before this header.

// Calculate cart item count for display
$cart_item_count = 0;
if (!empty($_SESSION['cart'])) {
    $cart_item_count = count($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? trans('business_name'); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1><a href="index.php"><?php echo trans('business_name'); ?></a></h1>
            </div>
            <ul class="nav-links">
                <li><a href="menu.php?category=sweets_snacks"><?php echo trans('nav_sweets_snacks'); ?></a></li>
                <li><a href="menu.php?category=made_to_order"><?php echo trans('nav_made_to_order'); ?></a></li>
                <li><a href="catering.php"><?php echo trans('nav_catering'); ?></a></li>
            </ul>
            <div class="nav-right">
                <div class="language-selector">
                    <a href="?lang=en">English</a> |
                    <a href="?lang=hi">हिंदी</a> |
                    <a href="?lang=mr">मराठी</a>
                </div>
                <a href="cart.php" class="cart-link">Cart (<?php echo $cart_item_count; ?>)</a>
            </div>
        </nav>
    </header>
    <main>
