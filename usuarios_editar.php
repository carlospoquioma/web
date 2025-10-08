<?php
include("conexion.php");

$id = $_GET['id'];

$sql = "SELECT id_usuario, nombre, email, rol, sucursal FROM usuarios WHERE id_usuario=$id LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    echo json_encode($result->fetch_assoc());
} else {
    echo "{}";
}

$conn->close();
?>
