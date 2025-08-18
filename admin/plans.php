<?php
require_once 'header.php';

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$plan = null;
$plan_items = [];

// Handle POST requests for CUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? $action;
    $id = $_POST['id'] ?? null;

    if ($action === 'delete' && !empty($id)) {
        $pdo->beginTransaction();
        $stmt_items = $pdo->prepare('DELETE FROM plan_items WHERE plan_id = ?');
        $stmt_items->execute([$id]);
        $stmt_plan = $pdo->prepare('DELETE FROM plans WHERE id = ?');
        $stmt_plan->execute([$id]);
        $pdo->commit();
    } else {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price_per_person = $_POST['price_per_person'] ?? 0;
        $product_ids = $_POST['product_ids'] ?? [];

        if (!empty($id)) { // Update
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('UPDATE plans SET name=?, description=?, price_per_person=? WHERE id=?');
            $stmt->execute([$name, $description, $price_per_person, $id]);

            // Re-create associations
            $stmt_delete_items = $pdo->prepare('DELETE FROM plan_items WHERE plan_id = ?');
            $stmt_delete_items->execute([$id]);

            $stmt_insert_items = $pdo->prepare('INSERT INTO plan_items (plan_id, product_id) VALUES (?, ?)');
            foreach ($product_ids as $product_id) {
                $stmt_insert_items->execute([$id, $product_id]);
            }
            $pdo->commit();
        } else { // Create
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('INSERT INTO plans (name, description, price_per_person) VALUES (?, ?, ?)');
            $stmt->execute([$name, $description, $price_per_person]);
            $new_plan_id = $pdo->lastInsertId();

            $stmt_insert_items = $pdo->prepare('INSERT INTO plan_items (plan_id, product_id) VALUES (?, ?)');
            foreach ($product_ids as $product_id) {
                $stmt_insert_items->execute([$new_plan_id, $product_id]);
            }
            $pdo->commit();
        }
    }
    header('Location: plans.php');
    exit;
}

// Fetch data for edit form
if ($action === 'edit' && !empty($id)) {
    $stmt = $pdo->prepare("SELECT * FROM plans WHERE id = ?");
    $stmt->execute([$id]);
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt_items = $pdo->prepare("SELECT product_id FROM plan_items WHERE plan_id = ?");
    $stmt_items->execute([$id]);
    $plan_items = $stmt_items->fetchAll(PDO::FETCH_COLUMN);
}

// Fetch all products for the form
$products_stmt = $pdo->query("SELECT id, name FROM products ORDER BY name ASC");
$all_products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h2>Catering Plan Management</h2>

<?php
switch ($action) {
    case 'new':
    case 'edit':
        ?>
        <h3><?php echo $action === 'edit' ? 'Edit Plan' : 'Add New Plan'; ?></h3>
        <form action="plans.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($plan['id'] ?? ''); ?>">
            <div class="form-group">
                <label for="name">Plan Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($plan['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($plan['description'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price_per_person">Price Per Person</label>
                <input type="number" step="0.01" id="price_per_person" name="price_per_person" value="<?php echo htmlspecialchars($plan['price_per_person'] ?? '0.00'); ?>" required>
            </div>
            <div class="form-group">
                <label>Included Products</label>
                <div class="product-checklist" style="height: 200px; overflow-y: auto; border: 1px solid var(--color-medium-gray);">
                    <?php foreach ($all_products as $product): ?>
                    <label>
                        <input type="checkbox" name="product_ids[]" value="<?php echo $product['id']; ?>" <?php echo in_array($product['id'], $plan_items) ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($product['name']); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="btn"><?php echo $action === 'edit' ? 'Update Plan' : 'Add Plan'; ?></button>
            <a href="plans.php" style="margin-left: 1rem;">Cancel</a>
        </form>
        <?php
        break;

    default: // 'list'
        $plans_stmt = $pdo->query("SELECT * FROM plans ORDER BY id DESC");
        $plans = $plans_stmt->fetchAll(PDO::FETCH_ASSOC);
        $items_stmt = $pdo->prepare("SELECT p.name FROM products p JOIN plan_items pi ON p.id = pi.product_id WHERE pi.plan_id = ?");
        ?>
        <p><a href="plans.php?action=new" class="btn">Add New Plan</a></p>
        <table style="width: 100%; border-collapse: collapse;">
             <thead>
                <tr style="background-color: var(--color-light-gray);">
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">ID</th>
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Name</th>
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Price/Person</th>
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Included Items</th>
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($plans as $plan):
                    $items_stmt->execute([$plan['id']]);
                    $items = $items_stmt->fetchAll(PDO::FETCH_COLUMN);
                ?>
                <tr>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($plan['id']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($plan['name']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);">$<?php echo htmlspecialchars(number_format($plan['price_per_person'], 2)); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars(implode(', ', $items)); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);">
                        <a href="plans.php?action=edit&id=<?php echo $plan['id']; ?>">Edit</a> |
                        <form action="plans.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $plan['id']; ?>">
                            <button type="submit" style="background:none; border:none; padding:0; color: #0645ad; text-decoration:underline; cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        break;
}
?>

<?php require_once 'footer.php'; ?>
