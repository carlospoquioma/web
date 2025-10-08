<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = intval($_POST['id']);
    $sku        = $_POST['sku'];
    $marca      = $_POST['marca'];
    $estilo     = $_POST['estilo'];
    $color      = $_POST['color'];
    $talla      = $_POST['talla'];
    $statu      = $_POST['statu'];
    $campana    = $_POST['campana'];
    $cantidad   = intval($_POST['cantidad']);
    $tipo_falla = $_POST['tipo_falla'];
    $nro_guia   = $_POST['nro_guia'];
    $sucursal   = $_POST['sucursal'];
    $condicion  = $_POST['condicion'];

    $sql = "UPDATE productos_observados SET 
                sku=?, marca=?, estilo=?, color=?, talla=?, 
                statu=?, campana=?, cantidad=?, tipo_falla=?, 
                nro_guia=?, sucursal=?, condicion=? 
            WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssi", 
        $sku, $marca, $estilo, $color, $talla, 
        $statu, $campana, $cantidad, $tipo_falla, 
        $nro_guia, $sucursal, $condicion, $id
    );

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "❌ Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
