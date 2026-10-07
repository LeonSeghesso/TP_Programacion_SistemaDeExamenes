<?php
session_start();
header('Content-Type: application/json');
include("conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['exito' => false, 'sesion' => false, 'mensaje' => 'Tenés que iniciar sesión primero']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

try {
    $stmt = $conn->prepare(
        "SELECT e.id, e.nombreExamen, COUNT(p.id) AS cantidad
         FROM examen e
         LEFT JOIN preguntas p ON p.id_examen = e.id
         WHERE e.id_usuario = ?
         GROUP BY e.id, e.nombreExamen
         ORDER BY e.id DESC"
    );
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $examenes = [];
    while ($fila = $resultado->fetch_assoc()) {
        $examenes[] = [
            'id' => (int)$fila['id'],
            'nombreExamen' => $fila['nombreExamen'],
            'cantidad' => (int)$fila['cantidad']
        ];
    }

    echo json_encode(['exito' => true, 'examenes' => $examenes]);
    $stmt->close();
} catch (Throwable $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se pudieron cargar los exámenes']);
}

$conn->close();
?>
