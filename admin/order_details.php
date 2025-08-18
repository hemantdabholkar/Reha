<?php
require_once 'header.php';

$order_id = $_GET['id'] ?? $_POST['order_id'] ?? null;
$success_message = '';

$allowed_statuses = ['pending', 'preparing', 'ready for delivery', 'delivered', 'payment received'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    // Fetch order details before updating to get customer email
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order_for_email = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order_id && $order_for_email && in_array($new_status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);

        // Send notification email
        require_once '../lib/email.php';
        $subject = "Update on your Pragati Mahila Udyog Order (#" . $order_id . ")";
        $email_body = "<p>Hi " . htmlspecialchars($order_for_email['customer_name']) . ",</p>";
        $email_body .= "<p>The status of your order (#" . $order_id . ") has been updated to: <strong>" . htmlspecialchars(ucwords($new_status)) . "</strong>.</p>";
        $email_body .= "<p>Thank you for your patience!</p>";

        $email_sent = send_order_email($order_for_email['customer_email'], $order_for_email['customer_name'], $subject, $email_body);

        $success_message = "Order status updated successfully!";
        if (!$email_sent) {
            $success_message .= " (But failed to send notification email.)";
        }
    }
}

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

<?php if ($success_message): ?>
    <div class="message success" style="background-color: var(--color-success-bg); color: var(--color-success-text); padding: 1rem; border-radius: 4px; margin-bottom: 1rem;"><?php echo $success_message; ?></div>
<?php endif; ?>

<div style="display: flex; gap: 2rem;">
    <div style="flex: 2;">
        <h4>Customer & Order Info</h4>
        <p><strong>Order Date:</strong> <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($order['created_at']))); ?></p>
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?> (<?php echo htmlspecialchars($order['customer_email']); ?>)</p>
        <p><strong>Total Price:</strong> $<?php echo htmlspecialchars(number_format($order['total_price'], 2)); ?></p>
    </div>
    <div style="flex: 1;">
        <h4>Update Status</h4>
        <form action="order_details.php?id=<?php echo $order_id; ?>" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <div class="form-group">
                <label for="status"><strong>Current Status:</strong> <?php echo htmlspecialchars(ucwords($order['status'])); ?></label>
                <select name="status" id="status" style="padding: 0.5rem; width: 100%;">
                    <?php foreach ($allowed_statuses as $status): ?>
                        <option value="<?php echo $status; ?>" <?php echo ($order['status'] === $status) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(ucwords($status)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="update_status" class="btn">Update Status</button>
        </form>
    </div>
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
