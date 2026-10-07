<?php
header('Content-Type: application/json');
include("conexion.php");

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($usuario) || empty($password)) {
    echo json_encode(['exito' => false, 'mensaje' => 'Campos vacíos']);
    exit;
}

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
