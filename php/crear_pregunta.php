<?php
session_start();
header('Content-Type: application/json');
include("conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['exito' => false, 'sesion' => false, 'mensaje' => 'Tenés que iniciar sesión primero']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_examen = (int)($_POST['id_examen'] ?? 0);
$pregunta = trim($_POST['pregunta'] ?? '');

if ($pregunta === '' || mb_strlen($pregunta) > 1000) {
    echo json_encode(['exito' => false, 'mensaje' => 'La pregunta es obligatoria (máximo 1000 caracteres)']);
    exit;
}

try {
    // Solo se pueden agregar preguntas a un examen propio
    $stmt = $conn->prepare("SELECT id FROM examen WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_examen, $id_usuario);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(['exito' => false, 'mensaje' => 'Examen no encontrado']);
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO preguntas (id_usuario, id_examen, pregunta) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_usuario, $id_examen, $pregunta);
    $stmt->execute();
    echo json_encode(['exito' => true, 'id' => $stmt->insert_id, 'mensaje' => 'Pregunta agregada']);
    $stmt->close();
} catch (Throwable $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se pudo agregar la pregunta']);
}

$conn->close();
?>
