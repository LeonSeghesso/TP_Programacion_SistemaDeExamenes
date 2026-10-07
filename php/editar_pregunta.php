<?php
session_start();
header('Content-Type: application/json');
include("conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['exito' => false, 'sesion' => false, 'mensaje' => 'Tenés que iniciar sesión primero']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_pregunta = (int)($_POST['id'] ?? 0);
$pregunta = trim($_POST['pregunta'] ?? '');

if ($id_pregunta <= 0 || $pregunta === '' || mb_strlen($pregunta) > 1000) {
    echo json_encode(['exito' => false, 'mensaje' => 'La pregunta es obligatoria (máximo 1000 caracteres)']);
    exit;
}

try {
    // Solo se puede editar una pregunta propia
    $stmt = $conn->prepare("SELECT id FROM preguntas WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_pregunta, $id_usuario);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(['exito' => false, 'mensaje' => 'Pregunta no encontrada']);
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE preguntas SET pregunta = ? WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("sii", $pregunta, $id_pregunta, $id_usuario);
    $stmt->execute();
    echo json_encode(['exito' => true, 'mensaje' => 'Pregunta actualizada']);
    $stmt->close();
} catch (Throwable $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se pudo actualizar la pregunta']);
}

$conn->close();
?>
