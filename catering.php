<?php
require_once 'init.php';

$page_title = trans('catering_title') . ' - ' . trans('business_name');
$message = '';
$message_type = ''; // 'success' or 'error'
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $event_date = filter_input(INPUT_POST, 'event_date', FILTER_SANITIZE_STRING);
    $guests = filter_input(INPUT_POST, 'guests', FILTER_VALIDATE_INT);
    $plan_id = filter_input(INPUT_POST, 'plan_id', FILTER_VALIDATE_INT);
    $requests = filter_input(INPUT_POST, 'requests', FILTER_SANITIZE_STRING);

    // Basic validation
    if (empty($name)) $errors['name'] = trans('form_field_required');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = trans('form_field_required');
    if (empty($event_date)) $errors['event_date'] = trans('form_field_required');
    if ($guests === false || $guests < 1) $errors['guests'] = trans('form_field_required');
    if (empty($plan_id)) $errors['plan_id'] = trans('form_error_no_plan');

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO catering_requests (name, email, phone, event_date, guests, plan_id, requests) VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$name, $email, $phone, $event_date, $guests, $plan_id, $requests]);

            $message = trans('form_success_message');
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = trans('form_error_message') . ': ' . $e->getMessage();
            $message_type = 'error';
        }
    } else {
        $message = trans('form_error_message');
        $message_type = 'error';
    }
}

// Fetch all plans and their items
$plans_query = $pdo->query('SELECT * FROM plans ORDER BY price_per_person ASC');
$plans = $plans_query->fetchAll(PDO::FETCH_ASSOC);

$plan_items_query = $pdo->prepare('
    SELECT p.name FROM products p
    JOIN plan_items pi ON p.id = pi.product_id
    WHERE pi.plan_id = ?
');

foreach ($plans as $key => $plan) {
    $plan_items_query->execute([$plan['id']]);
    $plans[$key]['items'] = $plan_items_query->fetchAll(PDO::FETCH_COLUMN);
}

require_once 'header.php';
?>

<section id="catering-page">
    <h2><?php echo trans('catering_title'); ?></h2>
    <p><?php echo trans('catering_page_intro'); ?></p>

    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($message_type !== 'success'): ?>
    <form action="catering.php" method="POST" class="catering-form">
        <div class="plan-selection">
            <h3><?php echo trans('catering_choose_plan_title'); ?></h3>
            <?php if(isset($errors['plan_id'])): ?><span class="error"><?php echo $errors['plan_id']; ?></span><?php endif; ?>
            <div class="plan-grid">
                <?php foreach ($plans as $plan): ?>
                <label class="plan-card">
                    <input type="radio" name="plan_id" value="<?php echo $plan['id']; ?>">
                    <div class="plan-card-content">
                        <h4><?php echo htmlspecialchars($plan['name']); ?></h4>
                        <p><?php echo htmlspecialchars($plan['description']); ?></p>
                        <p><strong>$<?php echo htmlspecialchars(number_format($plan['price_per_person'], 2)); ?> <?php echo trans('plan_price_per_person'); ?></strong></p>
                        <ul>
                            <?php foreach ($plan['items'] as $item): ?>
                            <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="details-form" style="display: none;">
            <h3><?php echo trans('form_details_title'); ?></h3>
            <div class="form-group">
                <label for="name"><?php echo trans('form_name'); ?></label>
                <input type="text" id="name" name="name" required>
                <?php if(isset($errors['name'])): ?><span class="error"><?php echo $errors['name']; ?></span><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email"><?php echo trans('form_email'); ?></label>
                <input type="email" id="email" name="email" required>
                <?php if(isset($errors['email'])): ?><span class="error"><?php echo $errors['email']; ?></span><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="phone"><?php echo trans('form_phone'); ?></label>
                <input type="tel" id="phone" name="phone">
            </div>
            <div class="form-group">
                <label for="event_date"><?php echo trans('form_event_date'); ?></label>
                <input type="date" id="event_date" name="event_date" required>
                <?php if(isset($errors['event_date'])): ?><span class="error"><?php echo $errors['event_date']; ?></span><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="guests"><?php echo trans('form_guests'); ?></label>
                <input type="number" id="guests" name="guests" min="1" required>
                <?php if(isset($errors['guests'])): ?><span class="error"><?php echo $errors['guests']; ?></span><?php endif; ?>
            </div>
            <div class="form-group">
                <label for="requests"><?php echo trans('form_requests'); ?></label>
                <textarea id="requests" name="requests" rows="4"></textarea>
            </div>
            <button type="submit" class="btn"><?php echo trans('form_submit'); ?></button>
        </div>
    </form>
    <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const planRadios = document.querySelectorAll('input[name="plan_id"]');
    const detailsForm = document.getElementById('details-form');

    planRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                detailsForm.style.display = 'block';
            }
        });
    });
});
</script>

<?php require_once 'footer.php'; ?>
