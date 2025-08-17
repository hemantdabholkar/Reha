<?php
require_once 'init.php';

// Set the page title for the header
$page_title = trans('cart_title') . ' - ' . trans('business_name');

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            $quantity = $_SESSION['cart'][$product['id']];
            $subtotal = $product['price'] * $quantity;
            $total_price += $subtotal;

            $cart_items[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image_url' => $product['image_url'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    }
}

require_once 'header.php';
?>

<section id="cart-page">
    <h2><?php echo trans('cart_title'); ?></h2>

    <?php if (empty($cart_items)): ?>
        <p><?php echo trans('cart_empty'); ?> <a href="menu.php?category=sweets_snacks"><?php echo trans('cart_continue_shopping'); ?></a>.</p>
    <?php else: ?>
        <form action="cart_actions.php" method="POST">
        <input type="hidden" name="action" value="update">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: var(--color-light-gray);">
                    <th colspan="2" style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo trans('cart_product'); ?></th>
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo trans('cart_price'); ?></th>
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo trans('cart_quantity'); ?></th>
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo trans('cart_subtotal'); ?></th>
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 80px; height: auto;"></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($item['name']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);">$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><input type="number" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="0" style="width: 60px;"></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);">$<?php echo htmlspecialchars(number_format($item['subtotal'], 2)); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><a href="cart_actions.php?action=remove&product_id=<?php echo $item['id']; ?>"><?php echo trans('cart_remove'); ?></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="text-align: right; margin-top: 1rem;">
            <button type="submit" class="btn"><?php echo trans('cart_update'); ?></button>
        </div>
        </form>
        <div style="text-align: right; margin-top: 2rem;">
            <h3><?php echo trans('cart_total'); ?>: $<?php echo htmlspecialchars(number_format($total_price, 2)); ?></h3>
            <a href="checkout.php" class="btn"><?php echo trans('cart_checkout'); ?></a>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'footer.php'; ?>
