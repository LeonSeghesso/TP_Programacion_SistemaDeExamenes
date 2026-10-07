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

try {
    // El examen tiene que ser del usuario logueado
    $stmt = $conn->prepare("SELECT nombreExamen FROM examen WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_examen, $id_usuario);
    $stmt->execute();
    $examen = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$examen) {
        echo json_encode(['exito' => false, 'mensaje' => 'Examen no encontrado']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, pregunta FROM preguntas WHERE id_examen = ? AND id_usuario = ? ORDER BY id ASC");
    $stmt->bind_param("ii", $id_examen, $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $preguntas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $preguntas[] = ['id' => (int)$fila['id'], 'pregunta' => $fila['pregunta']];
    }
    $stmt->close();

    echo json_encode([
        'exito' => true,
        'nombreExamen' => $examen['nombreExamen'],
        'preguntas' => $preguntas
    ]);
} catch (Throwable $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se pudieron cargar las preguntas']);
}

$conn->close();
?>
