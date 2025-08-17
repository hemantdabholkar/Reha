<?php
// database.php

class Database {
    private $pdo;

    public function __construct($db_path = 'pragati.sqlite') {
        try {
            // Ensure the path is absolute to the project root
            $absolute_db_path = __DIR__ . '/' . $db_path;
            $this->pdo = new PDO('sqlite:' . $absolute_db_path);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function createTables() {
        $commands = [
            'CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT,
                category TEXT NOT NULL,
                price REAL,
                image_url TEXT
            )',
            'CREATE TABLE IF NOT EXISTS catering_requests (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                event_date TEXT,
                guests INTEGER,
                requests TEXT,
                plan_id INTEGER,
                submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (plan_id) REFERENCES plans(id)
            )',
            'CREATE TABLE IF NOT EXISTS plans (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT,
                price_per_person REAL NOT NULL
            )',
            'CREATE TABLE IF NOT EXISTS plan_items (
                plan_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                PRIMARY KEY (plan_id, product_id),
                FOREIGN KEY (plan_id) REFERENCES plans(id),
                FOREIGN KEY (product_id) REFERENCES products(id)
            )',
            'CREATE TABLE IF NOT EXISTS admins (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL
            )',
            'CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                customer_name TEXT NOT NULL,
                customer_email TEXT NOT NULL,
                total_price REAL NOT NULL,
                status TEXT NOT NULL DEFAULT "pending",
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )',
            'CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL,
                price REAL NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id),
                FOREIGN KEY (product_id) REFERENCES products(id)
            )'
        ];

        foreach ($commands as $command) {
            $this->pdo->exec($command);
        }
    }
}

// Initialize the database and create tables if they don't exist
$db = new Database();
$db->createTables();

?>
