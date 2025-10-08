<?php
include("conexion.php");

$id = $_POST['id'];

$sql = "DELETE FROM usuarios WHERE id_usuario=$id";
if ($conn->query($sql) === TRUE) {
    echo "Usuario eliminado correctamente";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
