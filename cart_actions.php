<?php
require_once 'init.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$product_id = $_POST['product_id'] ?? $_GET['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if (!$action || !$product_id) {
    header('Location: index.php'); // Redirect home if action/product is missing
    exit;
}

switch ($action) {
    case 'add':
        // If product is already in cart, increment quantity, otherwise add it
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        break;

    case 'update':
        // Handle updating multiple quantities from the cart page
        if (is_array($_POST['quantity'])) {
            foreach ($_POST['quantity'] as $p_id => $qty) {
                $qty = (int)$qty;
                if ($qty > 0) {
                    $_SESSION['cart'][$p_id] = $qty;
                } else {
                    unset($_SESSION['cart'][$p_id]);
                }
            }
        } else { // Handle single quantity update (not currently used, but good practice)
            $quantity = (int)$quantity;
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
        $redirect_url = 'cart.php'; // Always redirect to cart after update
        break;

    case 'remove':
        unset($_SESSION['cart'][$product_id]);
        break;
}

// Redirect back to the previous page, or to the cart page as a fallback
$redirect_url = $_SERVER['HTTP_REFERER'] ?? 'cart.php';
header('Location: ' . $redirect_url);
exit;
?>
