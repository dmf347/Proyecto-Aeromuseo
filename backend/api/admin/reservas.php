<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $pdo = getConnection();
        // JOIN con usuarios para obtener el nombre
        $stmt = $pdo->query("
            SELECT r.*, u.nombre as usuario_nombre, u.email as usuario_email 
            FROM reservas r 
            JOIN usuarios u ON r.usuario_id = u.id 
            ORDER BY r.created_at DESC
        ");
        echo json_encode($stmt->fetchAll());
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;
    $estado = $data['estado'] ?? null;

    if (!$id || !$estado) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Faltan datos']);
        exit;
    }

    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("UPDATE reservas SET estado = :estado WHERE id = :id");
        if ($stmt->execute(['estado' => $estado, 'id' => $id])) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
