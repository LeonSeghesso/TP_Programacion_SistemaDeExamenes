<?php
header('Content-Type: application/json');
include("conexion.php");

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($usuario) || empty($password)) {
    echo json_encode(['exito' => false, 'mensaje' => 'Campos vacíos']);
    exit;
}

// No permitir dos usuarios con el mismo nombre
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'Ese nombre de usuario ya está en uso']);
    exit;
}
$stmt->close();

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO usuarios (usuario, password) VALUES (?, ?)");
$stmt->bind_param("ss", $usuario, $hash);

if ($stmt->execute()) {
    echo json_encode(['exito' => true, 'id' => $stmt->insert_id, 'mensaje' => 'Guardado!']);
} else {
    echo json_encode(['exito' => false, 'mensaje' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
