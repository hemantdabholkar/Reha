<?php
require_once 'header.php'; // Includes session check, init.php, and basic HTML header

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$product = null;

// Handle form submissions for Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $image_url = $_POST['image_url'] ?? '';
    $id = $_POST['id'] ?? null;

    if (!empty($id)) { // Update
        $stmt = $pdo->prepare('UPDATE products SET name=?, description=?, category=?, price=?, image_url=? WHERE id=?');
        $stmt->execute([$name, $description, $category, $price, $image_url, $id]);
    } else { // Create
        $stmt = $pdo->prepare('INSERT INTO products (name, description, category, price, image_url) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $description, $category, $price, $image_url]);
    }
    header('Location: products.php');
    exit;
}

// Handle Delete action - now part of POST handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_to_delete = $_POST['id'] ?? null;
    if ($id_to_delete) {
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id_to_delete]);
    }
    header('Location: products.php');
    exit;
}

if ($action === 'edit' && !empty($id)) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<h2>Product Management</h2>

<?php
// Main switch to control the view
switch ($action) {
    case 'new':
    case 'edit':
        ?>
        <h3><?php echo $action === 'edit' ? 'Edit Product' : 'Add New Product'; ?></h3>
        <form action="products.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id'] ?? ''); ?>">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="sweets_snacks" <?php echo ($product['category'] ?? '') === 'sweets_snacks' ? 'selected' : ''; ?>>Dry Sweets & Snacks</option>
                    <option value="made_to_order" <?php echo ($product['category'] ?? '') === 'made_to_order' ? 'selected' : ''; ?>>Made to Order</option>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($product['price'] ?? '0.00'); ?>" required>
            </div>
            <div class="form-group">
                <label for="image_url">Image URL</label>
                <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($product['image_url'] ?? ''); ?>">
            </div>
            <button type="submit" class="btn"><?php echo $action === 'edit' ? 'Update Product' : 'Add Product'; ?></button>
            <a href="products.php" style="margin-left: 1rem;">Cancel</a>
        </form>
        <?php
        break;

    default: // 'list'
        $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <p><a href="products.php?action=new" class="btn">Add New Product</a></p>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: var(--color-light-gray);">
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">ID</th>
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Name</th>
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Category</th>
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Price</th>
                    <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($product['id']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($product['name']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($product['category']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);">$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);">
                        <a href="products.php?action=edit&id=<?php echo $product['id']; ?>">Edit</a> |
                        <form action="products.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
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
