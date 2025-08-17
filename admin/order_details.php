<?php
require_once 'header.php';

$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    echo "<h2>Error</h2><p>No order ID specified.</p>";
    require_once 'footer.php';
    exit;
}

// Fetch the main order details
$order_stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$order_stmt->execute([$order_id]);
$order = $order_stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<h2>Error</h2><p>Order not found.</p>";
    require_once 'footer.php';
    exit;
}

// Fetch the items for this order
$items_stmt = $pdo->prepare("
    SELECT oi.quantity, oi.price, p.name AS product_name
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$items_stmt->execute([$order_id]);
$items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h2>Order Details #<?php echo htmlspecialchars($order['id']); ?></h2>

<div>
    <p><strong>Order Date:</strong> <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($order['created_at']))); ?></p>
    <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?> (<?php echo htmlspecialchars($order['customer_email']); ?>)</p>
    <p><strong>Total Price:</strong> $<?php echo htmlspecialchars(number_format($order['total_price'], 2)); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
</div>

<h3 style="margin-top: 2rem;">Items in this Order</h3>
<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: var(--color-light-gray);">
            <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Product</th>
            <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Quantity</th>
            <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Price per Item</th>
            <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($item['product_name']); ?></td>
            <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($item['quantity']); ?></td>
            <td style="padding: 8px; border: 1px solid var(--color-medium-gray);">$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
            <td style="padding: 8px; border: 1px solid var(--color-medium-gray);">$<?php echo htmlspecialchars(number_format($item['price'] * $item['quantity'], 2)); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p style="margin-top: 2rem;"><a href="orders.php">&larr; Back to All Orders</a></p>

<?php require_once 'footer.php'; ?>
