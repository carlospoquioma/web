<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();
session_start(); // Necesario para usar variables de sesión

include("conexion.php");

$id              = $_POST['id'] ?? "";
$sku             = $_POST['sku'] ?? "";
$marca           = $_POST['marca'] ?? "";
$estilo          = $_POST['estilo'] ?? "";
$color           = $_POST['color'] ?? "";
$talla           = $_POST['talla'] ?? "";
$statu           = $_POST['statu'] ?? "";
$campana         = $_POST['campana'] ?? "";
$cantidad        = $_POST['cantidad'] ?? "";
$tipo_falla      = $_POST['tipo_falla'] ?? "";
$fecha_recepcion = $_POST['fecha_recepcion'] ?? "";
$nro_guia        = $_POST['nro_guia'] ?? "";
$sucursal        = $_POST['sucursal'] ?? "";
$condicion       = $_POST['condicion'] ?? "en revision";

// Verificar perfil del usuario
$perfilSesion = $_SESSION['rol'] ?? "";
$sucursalSesion = $_SESSION['sucursal'] ?? "";

// Si el perfil es 'almacen', se usa la sucursal de la sesión
if (strtolower($perfilSesion) === "almacen" && !empty($sucursalSesion)) {
    $sucursal = $sucursalSesion;
}

// Manejo de foto
$foto = "";
if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
    $carpeta = "uploads/";
    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0777, true);
    }
    $nombreArchivo = time() . "_" . basename($_FILES['foto']['name']);
    $rutaDestino = $carpeta . $nombreArchivo;
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
        $foto = $rutaDestino;
    }
}

// INSERT o UPDATE según exista ID
if ($id == "") {
    $sql = "INSERT INTO productos_observados 
        (sku, marca, estilo, color, talla, statu, campana, cantidad, tipo_falla, 
         fecha_recepcion, fecha_registro, nro_guia, sucursal, foto, condicion) 
        VALUES 
        ('$sku','$marca','$estilo','$color','$talla','$statu','$campana','$cantidad',
         '$tipo_falla','$fecha_recepcion',NOW(),'$nro_guia','$sucursal','$foto','$condicion')";
} else {
    if ($foto != "") {
        $sql = "UPDATE productos_observados SET 
            sku='$sku', marca='$marca', estilo='$estilo', color='$color', talla='$talla',
            statu='$statu', campana='$campana', cantidad='$cantidad',
            tipo_falla='$tipo_falla', fecha_recepcion='$fecha_recepcion',
            nro_guia='$nro_guia', sucursal='$sucursal', foto='$foto',
            condicion='$condicion'
            WHERE id=$id";
    } else {
        $sql = "UPDATE productos_observados SET 
            sku='$sku', marca='$marca', estilo='$estilo', color='$color', talla='$talla',
            statu='$statu', campana='$campana', cantidad='$cantidad',
            tipo_falla='$tipo_falla', fecha_recepcion='$fecha_recepcion',
            nro_guia='$nro_guia', sucursal='$sucursal',
            condicion='$condicion'
            WHERE id=$id";
    }
}

if ($conn->query($sql) === TRUE) {
    echo "ok";
} else {
    echo "error: " . $conn->error;
}

$conn->close();
ob_end_flush();
?>
