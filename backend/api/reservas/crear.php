<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$usuario_id = $data['usuario_id'] ?? null;
$fecha_visita = $data['fecha_visita'] ?? null;
$num_personas = $data['num_personas'] ?? 1;
$comentarios = $data['comentarios'] ?? '';

if (!$usuario_id || !$fecha_visita) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
    exit;
}

try {
    $pdo = getConnection();
    
    $stmt = $pdo->prepare("
        INSERT INTO reservas (usuario_id, fecha_visita, num_personas, comentarios, estado) 
        VALUES (:usuario_id, :fecha_visita, :num_personas, :comentarios, 'pendiente')
    ");
    
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':fecha_visita', $fecha_visita);
    $stmt->bindParam(':num_personas', $num_personas);
    $stmt->bindParam(':comentarios', $comentarios);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Reserva solicitada correctamente.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al crear la reserva']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
