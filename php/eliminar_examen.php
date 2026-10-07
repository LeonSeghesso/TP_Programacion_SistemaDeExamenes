<?php
session_start();
header('Content-Type: application/json');
include("conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['exito' => false, 'sesion' => false, 'mensaje' => 'Tenés que iniciar sesión primero']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_examen = (int)($_POST['id'] ?? 0);

if ($id_examen <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'Examen inválido']);
    exit;
}

try {
    // Solo se puede eliminar un examen propio; sus preguntas se borran en cascada
    $stmt = $conn->prepare("DELETE FROM examen WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_examen, $id_usuario);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        echo json_encode(['exito' => false, 'mensaje' => 'Examen no encontrado']);
    } else {
        echo json_encode(['exito' => true, 'mensaje' => 'Examen eliminado']);
    }
    $stmt->close();
} catch (Throwable $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se pudo eliminar el examen']);
}

$conn->close();
?>
