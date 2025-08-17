<?php
require_once 'header.php';

// Fetch all orders
$stmt = $pdo->query("
    SELECT * FROM orders
    ORDER BY created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h2>Customer Orders</h2>

<div style="overflow-x:auto;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: var(--color-light-gray);">
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Order ID</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Date</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Customer Name</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Customer Email</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Total Price</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Status</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 1rem;">No orders yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);">#<?php echo htmlspecialchars($order['id']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($order['created_at']))); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><a href="mailto:<?php echo htmlspecialchars($order['customer_email']); ?>"><?php echo htmlspecialchars($order['customer_email']); ?></a></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);">$<?php echo htmlspecialchars(number_format($order['total_price'], 2)); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($order['status']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);">
                        <a href="order_details.php?id=<?php echo $order['id']; ?>">View Details</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
