<?php
include("conexion.php");

// Recibir el ID por POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$response = [];

if ($id > 0) {
    // Consulta para eliminar
    $sql = "DELETE FROM productos_observados WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $response = [
            /*"status" => "success",*/
            "message" => "Producto eliminado correctamente."
           
        ];
    } else {
        $response = [
            "status" => "error",
            "message" => "Error al eliminar el producto: " . $stmt->error
        ];
    }

    $stmt->close();
} else {
    $response = [
        "status" => "error",
        "message" => "ID de producto no válido."
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

$conn->close();
?>
