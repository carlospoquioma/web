<?php
include("conexion.php");

$id_usuario = $_POST['id_usuario'];
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$password = $_POST['password'];
$rol = $_POST['rol'];
$sucursal = $_POST['sucursal'];

// --- NUEVO USUARIO ---
if (empty($id_usuario)) {

    if (empty($password)) {
        echo "Debe ingresar una contraseña";
        exit;
    }

    // Encriptar la contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Usamos consulta preparada para evitar inyección SQL
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol, sucursal) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $email, $hash, $rol, $sucursal);

    if ($stmt->execute()) {
        echo "✅ Usuario registrado correctamente";
    } else {
        echo "❌ Error al registrar usuario: " . $stmt->error;
    }

    $stmt->close();

} else {
    // --- EDITAR USUARIO ---

    if (!empty($password)) {
        // Si se cambia la contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, email=?, password=?, rol=?, sucursal=? WHERE id_usuario=?");
        $stmt->bind_param("sssssi", $nombre, $email, $hash, $rol, $sucursal, $id_usuario);
    } else {
        // Si no se cambia la contraseña
        $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, email=?, rol=?, sucursal=? WHERE id_usuario=?");
        $stmt->bind_param("ssssi", $nombre, $email, $rol, $sucursal, $id_usuario);
    }

    if ($stmt->execute()) {
        echo "✅ Usuario actualizado correctamente";
    } else {
        echo "❌ Error al actualizar usuario: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
