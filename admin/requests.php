<?php
require_once 'header.php';

// Fetch all catering requests, joining with plans table to get the plan name
$stmt = $pdo->query("
    SELECT
        cr.id,
        cr.submitted_at,
        cr.name AS customer_name,
        cr.email,
        cr.phone,
        cr.event_date,
        cr.guests,
        cr.requests,
        p.name AS plan_name
    FROM
        catering_requests cr
    LEFT JOIN
        plans p ON cr.plan_id = p.id
    ORDER BY
        cr.submitted_at DESC
");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h2>Catering Requests</h2>

<div style="overflow-x:auto;"> <!-- Make table horizontally scrollable on small screens -->
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: var(--color-light-gray);">
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">ID</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Date Submitted</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Customer Name</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Email</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Phone</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Event Date</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Guests</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Selected Plan</th>
                <th style="padding: 8px; border: 1px solid var(--color-medium-gray);">Special Requests</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($requests)): ?>
                <tr>
                    <td colspan="9" style="text-align:center; padding: 1rem;">No catering requests yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($requests as $request): ?>
                <tr>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($request['id']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($request['submitted_at']))); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($request['customer_name']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><a href="mailto:<?php echo htmlspecialchars($request['email']); ?>"><?php echo htmlspecialchars($request['email']); ?></a></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($request['phone']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($request['event_date']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($request['guests']); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><?php echo htmlspecialchars($request['plan_name'] ?? 'N/A'); ?></td>
                    <td style="padding: 8px; border: 1px solid var(--color-medium-gray);"><pre style="white-space: pre-wrap;"><?php echo htmlspecialchars($request['requests']); ?></pre></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
