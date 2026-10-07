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
$cantidad = (int)($_POST['cantidad'] ?? 0);

try {
    // El examen tiene que ser del usuario logueado
    $stmt = $conn->prepare("SELECT id FROM examen WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_examen, $id_usuario);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(['exito' => false, 'mensaje' => 'Examen no encontrado']);
        exit;
    }
    $stmt->close();

    // Cuántas preguntas tiene el examen
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM preguntas WHERE id_examen = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_examen, $id_usuario);
    $stmt->execute();
    $total = (int)$stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    if ($total === 0) {
        echo json_encode(['exito' => false, 'mensaje' => 'El examen no tiene preguntas para sortear']);
        exit;
    }

    if ($cantidad < 1 || $cantidad > $total) {
        echo json_encode(['exito' => false, 'mensaje' => 'Ingresá un número entre 1 y ' . $total]);
        exit;
    }

    // Sorteo: solo preguntas de este examen y de este usuario; cada fila sale una sola vez
    $stmt = $conn->prepare(
        "SELECT pregunta FROM preguntas
         WHERE id_examen = ? AND id_usuario = ?
         ORDER BY RAND()
         LIMIT ?"
    );
    $stmt->bind_param("iii", $id_examen, $id_usuario, $cantidad);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $preguntas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $preguntas[] = $fila['pregunta'];
    }
    $stmt->close();

    echo json_encode(['exito' => true, 'preguntas' => $preguntas]);
} catch (Throwable $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se pudo realizar el sorteo']);
}

$conn->close();
?>
