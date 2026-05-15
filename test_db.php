<?php
require_once __DIR__ . '/backend/config/database.php';
echo "Testing DB...\n";
try {
    $pdo = getConnection();
    echo "DB connected!\n";
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
