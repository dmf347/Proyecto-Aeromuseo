<?php
require 'backend/config/database.php';
$pdo = getConnection();
$stmt = $pdo->query('SHOW TABLES');
print_r($stmt->fetchAll());
