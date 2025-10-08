<?php
include("conexion.php");

$nombre = "Administrador";
$email = "admin@correo.com";
$password = "admin123";
$rol = "administrador";

$hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nombre, email, password, rol) 
        VALUES ('$nombre', '$email', '$hash', '$rol')";

if ($conn->query($sql) === TRUE) {
    echo "Administrador creado correctamente";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
