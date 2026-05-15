<?php
require 'backend/config/database.php';
$pdo = getConnection();
$stmt = $pdo->query('SELECT * FROM usuarios');
print_r($stmt->fetchAll());
