<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

include("conexion.php");

// Validar fechas recibidas
$fecha_inicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : "";
$fecha_fin = isset($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : "";

function valid_date($d){
    $t = DateTime::createFromFormat('Y-m-d', $d);
    return $t && $t->format('Y-m-d') === $d;
}

// Armar consulta
$where = "";
$params = [];
$types = "";

if ($fecha_inicio !== "" && $fecha_fin !== "" && valid_date($fecha_inicio) && valid_date($fecha_fin)) {
    $where = "WHERE fecha_registro BETWEEN ? AND ?";
    $params = [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'];
    $types = 'ss';
    $sql = "SELECT * FROM productos_observados $where ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $bind_names = [];
    $bind_names[] = $types;
    for ($i = 0; $i < count($params); $i++) $bind_names[] = &$params[$i];
    call_user_func_array([$stmt, 'bind_param'], $bind_names);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM productos_observados ORDER BY id DESC");
}

// Configurar encabezados para descargar como Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=reporte_productos_observados.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Encabezado de la tabla
echo "<table border='1'>";
echo "<tr style='background-color:#d9ead3; font-weight:bold;'>
        <th>ID</th>
        <th>SKU</th>
        <th>Marca</th>
        <th>Estilo</th>
        <th>Color</th>
        <th>Talla</th>
        <th>Status</th>
        <th>Campaña</th>
        <th>Cantidad</th>
        <th>Tipo Falla</th>
        <th>Fecha Registro</th>
        <th>Nro Guía</th>
        <th>Sucursal</th>
        <th>Condición</th>
      </tr>";

// Cuerpo del reporte
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['sku']}</td>
                <td>{$row['marca']}</td>
                <td>{$row['estilo']}</td>
                <td>{$row['color']}</td>
                <td>{$row['talla']}</td>
                <td>{$row['statu']}</td>
                <td>{$row['campana']}</td>
                <td>{$row['cantidad']}</td>
                <td>{$row['tipo_falla']}</td>
                <td>{$row['fecha_registro']}</td>
                <td>{$row['nro_guia']}</td>
                <td>{$row['sucursal']}</td>
                <td>{$row['condicion']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='14'>No hay datos disponibles.</td></tr>";
}

echo "</table>";
exit;
?>
