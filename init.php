<?php
// init.php

session_start();

// --- Language Configuration ---
$config = [
    'default_lang' => 'en',
    'allowed_langs' => ['en', 'hi', 'mr'],
];

// --- Language Loading Logic ---
$lang_code = $config['default_lang']; // Default language

// 1. Check for language in URL
if (isset($_GET['lang']) && in_array($_GET['lang'], $config['allowed_langs'])) {
    $lang_code = $_GET['lang'];
    $_SESSION['lang'] = $lang_code; // Store in session
}
// 2. Check for language in session
elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $config['allowed_langs'])) {
    $lang_code = $_SESSION['lang'];
}

// Load the language file
$lang_file = 'lang/' . $lang_code . '.php';
if (file_exists($lang_file)) {
    $translations = require $lang_file;
} else {
    // Fallback to default language if file not found
    $translations = require 'lang/' . $config['default_lang'] . '.php';
}

/**
 * Translation helper function.
 *
 * @param string $key The key for the translation string.
 * @return string The translated string or the key if not found.
 */
function trans($key) {
    global $translations;
    return isset($translations[$key]) ? $translations[$key] : $key;
}

// --- Database Initialization ---
require_once 'database.php';
$db = new Database();
$pdo = $db->getConnection();

?>
