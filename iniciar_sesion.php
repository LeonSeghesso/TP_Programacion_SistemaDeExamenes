<?php
header('Content-Type: application/json');
include("conexion.php");

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($usuario) || empty($password)) {
    echo json_encode(['exito' => false, 'mensaje' => 'Campos vacíos']);
    exit;
}

$stmt = $conn->prepare("SELECT id, password FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$fila = $resultado->fetch_assoc();

if ($fila && password_verify($password, $fila['password'])) {
    echo json_encode(['exito' => true, 'id' => $fila['id'], 'mensaje' => 'Bienvenido, ' . $usuario . '!']);
} else {
    echo json_encode(['exito' => false, 'mensaje' => 'Usuario o contraseña incorrectos']);
}

$stmt->close();
$conn->close();
?>
