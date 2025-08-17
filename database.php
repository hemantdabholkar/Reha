<?php
// database.php

class Database {
    private $pdo;

    public function __construct($db_path = 'pragati.sqlite') {
        try {
            $this->pdo = new PDO('sqlite:' . $db_path);
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
