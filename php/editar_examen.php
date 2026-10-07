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
$nombre = trim($_POST['nombreExamen'] ?? '');

if ($id_examen <= 0 || $nombre === '' || mb_strlen($nombre) > 100) {
    echo json_encode(['exito' => false, 'mensaje' => 'El nombre es obligatorio (máximo 100 caracteres)']);
    exit;
}

try {
    // Solo se puede editar un examen que pertenezca al usuario logueado
    $stmt = $conn->prepare("SELECT id FROM examen WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_examen, $id_usuario);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(['exito' => false, 'mensaje' => 'Examen no encontrado']);
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE examen SET nombreExamen = ? WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("sii", $nombre, $id_examen, $id_usuario);
    $stmt->execute();
    echo json_encode(['exito' => true, 'mensaje' => 'Examen actualizado']);
    $stmt->close();
} catch (Throwable $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se pudo actualizar el examen']);
}

$conn->close();
?>
