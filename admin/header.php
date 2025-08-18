<?php
require_once '../init.php';

// --- Auth Check ---
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Pragati Mahila Udyog</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { padding: 0; }
        .admin-header {
            background-color: var(--color-brown);
            color: var(--color-white);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .admin-nav a {
            color: var(--color-white);
            text-decoration: none;
            margin-left: 1.5rem;
        }
        .admin-nav a:hover {
            color: var(--color-turmeric);
        }
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h1>Admin Panel</h1>
        <nav class="admin-nav">
            <a href="index.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="plans.php">Catering Plans</a>
            <a href="requests.php">Catering Requests</a>
            <a href="orders.php">Customer Orders</a>
            <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</a>
        </nav>
    </header>
    <main class="admin-container">
