<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getConnection();

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM eventos ORDER BY fecha DESC, hora DESC");
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("INSERT INTO eventos (titulo, descripcion, fecha, hora, lugar, imagen_url) VALUES (:titulo, :descripcion, :fecha, :hora, :lugar, :imagen_url)");
    $success = $stmt->execute([
        'titulo' => $data['titulo'],
        'descripcion' => $data['descripcion'],
        'fecha' => $data['fecha'],
        'hora' => $data['hora'],
        'lugar' => $data['lugar'],
        'imagen_url' => $data['imagen_url'] ?? null
    ]);
    echo json_encode(['success' => $success, 'message' => $success ? 'Evento creado' : 'Error al crear evento']);
    exit;
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("UPDATE eventos SET titulo = :titulo, descripcion = :descripcion, fecha = :fecha, hora = :hora, lugar = :lugar, imagen_url = :imagen_url WHERE id = :id");
    $success = $stmt->execute([
        'titulo' => $data['titulo'],
        'descripcion' => $data['descripcion'],
        'fecha' => $data['fecha'],
        'hora' => $data['hora'],
        'lugar' => $data['lugar'],
        'imagen_url' => $data['imagen_url'] ?? null,
        'id' => $data['id']
    ]);
    echo json_encode(['success' => $success, 'message' => $success ? 'Evento actualizado' : 'Error al actualizar evento']);
    exit;
}

if ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM eventos WHERE id = :id");
        $success = $stmt->execute(['id' => $id]);
        echo json_encode(['success' => $success, 'message' => $success ? 'Evento eliminado' : 'Error al eliminar evento']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    }
    exit;
}
