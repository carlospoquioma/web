<?php
include("conexion.php");

$term = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($term == '') {
    echo '';
    exit;
}

$sql = "SELECT sku, marca, estilo, color, talla, statu, campana 
        FROM productos 
        WHERE sku LIKE ? OR estilo LIKE ? 
        LIMIT 20";

$stmt = $conn->prepare($sql);
$termLike = "%$term%";
$stmt->bind_param("ss", $termLike, $termLike);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['sku']}</td>
                <td>{$row['marca']}</td>
                <td>{$row['estilo']}</td>
                <td>{$row['color']}</td>
                <td>{$row['talla']}</td>
                <td>{$row['statu']}</td>
                <td>{$row['campana']}</td>
                <td class='text-center'>
                    <button type='button' class='btn btn-sm btn-success seleccionar'
                        data-sku='{$row['sku']}'
                        data-marca='{$row['marca']}'
                        data-estilo='{$row['estilo']}'
                        data-color='{$row['color']}'
                        data-talla='{$row['talla']}'
                        data-statu='{$row['statu']}'
                        data-campana='{$row['campana']}'>
                        Seleccionar
                    </button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center text-muted'>No se encontraron resultados</td></tr>";
}

$conn->close();
?>
