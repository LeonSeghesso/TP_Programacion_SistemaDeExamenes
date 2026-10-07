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

if ($id_pregunta <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'Pregunta inválida']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM preguntas WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_pregunta, $id_usuario);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        echo json_encode(['exito' => false, 'mensaje' => 'Pregunta no encontrada']);
    } else {
        echo json_encode(['exito' => true, 'mensaje' => 'Pregunta eliminada']);
    }
    $stmt->close();
} catch (Throwable $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se pudo eliminar la pregunta']);
}

$conn->close();
?>
