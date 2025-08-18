<?php
require_once 'init.php';

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$cart_items = [];
$total_price = 0;
$order_placed = false;

// --- Calculate Cart Totals ---
$product_ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($product_ids);
$products_in_cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($products_in_cart as $product) {
    $quantity = $_SESSION['cart'][$product['id']];
    $total_price += $product['price'] * $quantity;
    $cart_items[] = ['name' => $product['name'], 'quantity' => $quantity];
}

// --- Handle Checkout Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = filter_input(INPUT_POST, 'customer_name', FILTER_SANITIZE_STRING);
    $customer_email = filter_input(INPUT_POST, 'customer_email', FILTER_SANITIZE_EMAIL);

    if (empty($customer_name) || empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please fill in all fields with valid information.";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Create the order
            $order_stmt = $pdo->prepare('INSERT INTO orders (customer_name, customer_email, total_price) VALUES (?, ?, ?)');
            $order_stmt->execute([$customer_name, $customer_email, $total_price]);
            $order_id = $pdo->lastInsertId();

            // 2. Create the order items
            $item_stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
            foreach ($products_in_cart as $product) {
                $quantity = $_SESSION['cart'][$product['id']];
                $item_stmt->execute([$order_id, $product['id'], $quantity, $product['price']]);
            }

            $pdo->commit();

            // 3. Send invoice email
            require_once 'lib/email.php';
            $subject = "Your Pragati Mahila Udyog Order Confirmation (#" . $order_id . ")";

            $email_body = "<h1>Thank you for your order!</h1>";
            $email_body .= "<p>Hi " . htmlspecialchars($customer_name) . ",</p>";
            $email_body .= "<p>We've received your order (#" . $order_id . ") and will start preparing it shortly.</p>";
            $email_body .= "<h3>Order Summary:</h3><ul>";
            foreach ($products_in_cart as $product) {
                $quantity = $_SESSION['cart'][$product['id']];
                $email_body .= "<li>" . htmlspecialchars($product['name']) . " (x" . $quantity . ")</li>";
            }
            $email_body .= "</ul>";
            $email_body .= "<h4>Total: $" . number_format($total_price, 2) . "</h4>";
            $email_body .= "<p>We will contact you separately regarding payment.</p>";

            $email_sent = send_order_email($customer_email, $customer_name, $subject, $email_body);

            // 4. Clear the cart and show success
            unset($_SESSION['cart']);
            $order_placed = true;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "There was an error placing your order. Please try again.";
        }
    }
}

$page_title = 'Checkout - ' . trans('business_name');
require_once 'header.php';
?>

<section id="checkout-page">
    <h2>Checkout</h2>

    <?php if ($order_placed): ?>
        <div class="message success">
            <h3>Thank you for your order!</h3>
            <p>Your order has been placed successfully. Your order number is #<?php echo $order_id; ?>.</p>
            <p>We will contact you shortly to confirm the details.</p>
            <?php if (!$email_sent): ?>
                <p style="color: var(--color-error-text);">Note: We had trouble sending the confirmation email, but your order was received.</p>
            <?php endif; ?>
            <p><a href="index.php">Return to Homepage</a></p>
        </div>
    <?php else: ?>
        <div style="display: flex; gap: 2rem;">
            <div style="flex: 1;">
                <h3>Your Information</h3>
                <?php if (isset($error)): ?>
                    <div class="message error"><?php echo $error; ?></div>
                <?php endif; ?>
                <form action="checkout.php" method="POST">
                    <div class="form-group">
                        <label for="customer_name">Full Name</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_email">Email Address</label>
                        <input type="email" id="customer_email" name="customer_email" required>
                    </div>
                    <p><em>Payment will be arranged upon confirmation.</em></p>
                    <button type="submit" class="btn">Place Order</button>
                </form>
            </div>
            <div style="flex: 1;">
                <h3>Order Summary</h3>
                <ul>
                    <?php foreach ($cart_items as $item): ?>
                        <li><?php echo htmlspecialchars($item['name']); ?> (x<?php echo htmlspecialchars($item['quantity']); ?>)</li>
                    <?php endforeach; ?>
                </ul>
                <hr>
                <h4>Total: $<?php echo htmlspecialchars(number_format($total_price, 2)); ?></h4>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'footer.php'; ?>
