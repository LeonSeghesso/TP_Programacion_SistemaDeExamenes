<?php
session_start();
header('Content-Type: application/json');
include("conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['exito' => false, 'sesion' => false, 'mensaje' => 'Tenés que iniciar sesión primero']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nombre = trim($_POST['nombreExamen'] ?? '');

if ($nombre === '' || mb_strlen($nombre) > 100) {
    echo json_encode(['exito' => false, 'mensaje' => 'El nombre es obligatorio (máximo 100 caracteres)']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO examen (id_usuario, nombreExamen) VALUES (?, ?)");
    $stmt->bind_param("is", $id_usuario, $nombre);
    $stmt->execute();
    echo json_encode(['exito' => true, 'id' => $stmt->insert_id, 'mensaje' => 'Examen creado']);
    $stmt->close();
} catch (Throwable $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se pudo crear el examen']);
}

$conn->close();
?>
