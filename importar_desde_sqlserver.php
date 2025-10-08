<?php
/**
 * 🚀 IMPORTACIÓN MASIVA SQL Server → MySQL (versión optimizada)
 * Autor: ChatGPT (versión ajustada para José Carlos)
 * Uso: php importar_productos_cli.php
 */

ini_set('memory_limit', '2048M');
set_time_limit(0);
error_reporting(E_ALL);
date_default_timezone_set('America/Lima');

echo "\n=== 🚀 INICIO DE IMPORTACIÓN MASIVA DE PRODUCTOS ===\n";
$start_time = microtime(true);

// -------------------- CONEXIÓN MYSQL --------------------
$mysqli = new mysqli("localhost", "root", "", "sistema_web");
if ($mysqli->connect_errno) {
    die("❌ Error MySQL: " . $mysqli->connect_error . "\n");
}
$mysqli->set_charset("utf8mb4");

// 🔧 Optimización MySQL
$mysqli->query("SET autocommit=0");
$mysqli->query("SET unique_checks=0");
$mysqli->query("SET foreign_key_checks=0");
$mysqli->query("SET sql_mode=''");

// ✅ Asegurar índice único para evitar duplicados
$mysqli->query("ALTER TABLE productos ADD UNIQUE (SKU)");

// -------------------- CONEXIÓN SQL SERVER --------------------
$serverName = "tcp:190.12.74.148,1433";
$connectionInfo = [
    "Database" => "retaildata",
    "UID" => "tabledyn",
    "PWD" => "tabledyn2018",
    "CharacterSet" => "UTF-8",
    "TrustServerCertificate" => true
];

$conn_sqlsrv = sqlsrv_connect($serverName, $connectionInfo);
if (!$conn_sqlsrv) {
    die("❌ Error SQL Server: " . print_r(sqlsrv_errors(), true));
}

// -------------------- TOTAL DE REGISTROS --------------------
$res_total = sqlsrv_query($conn_sqlsrv, "SELECT COUNT(*) AS total FROM PRODUCT_JOEN_SHORTOK");
$row_total = sqlsrv_fetch_array($res_total, SQLSRV_FETCH_ASSOC);
$total_rows = (int)($row_total['total'] ?? 0);
sqlsrv_free_stmt($res_total);

//echo "Total de registros a importar: $total_rows\n";
//echo "===============================================\n";

if ($total_rows === 0) {
    exit("⚠️ No hay registros para importar.\n");
}

// -------------------- CONFIGURACIÓN --------------------
$batchSize   = 20000; // registros por bloque desde SQL Server
$insertBatch = 2000;  // registros por INSERT múltiple
$offset      = 0;
$processed   = 0;
$batch_num   = 0;

// -------------------- BUCLE PRINCIPAL --------------------
do {
    $batch_num++;
   // echo "🔹 Procesando bloque #$batch_num (desde fila $offset)...\n";

    $sql_sqlsrv = "
        SELECT SKU, Marca, Estilo, Color, Genero, Talla, Status AS Statu, Campaña AS Campana
        FROM (
            SELECT *, ROW_NUMBER() OVER (ORDER BY SKU) AS rn
            FROM PRODUCT_JOEN_SHORTOK
        ) AS t
        WHERE rn > ? AND rn <= ?
    ";
    $params = [$offset, $offset + $batchSize];
    $stmt_sqlsrv = sqlsrv_query($conn_sqlsrv, $sql_sqlsrv, $params);

    if ($stmt_sqlsrv === false) {
        die("❌ Error SQL Server: " . print_r(sqlsrv_errors(), true));
    }

    $values = [];
    $batchCount = 0;
    $numRows = 0;

    // 🟡 Iniciar transacción por bloque
    $mysqli->begin_transaction();

    while ($row = sqlsrv_fetch_array($stmt_sqlsrv, SQLSRV_FETCH_ASSOC)) {
        $sku     = addslashes(trim($row['SKU'] ?? ''));
        $marca   = addslashes(trim($row['Marca'] ?? ''));
        $estilo  = addslashes(trim($row['Estilo'] ?? ''));
        $color   = addslashes(trim($row['Color'] ?? ''));
        $genero  = addslashes(trim($row['Genero'] ?? ''));
        $talla   = addslashes(trim($row['Talla'] ?? ''));
        $statu   = addslashes(trim($row['Statu'] ?? ''));
        $campana = addslashes(trim($row['Campana'] ?? ''));

        if ($sku === '') continue; // evitar registros sin SKU

        $values[] = "('$sku','$marca','$estilo','$color','$genero','$talla','$statu','$campana')";
        $processed++;
        $numRows++;
        $batchCount++;

        // 🔹 Ejecutar inserción múltiple cada $insertBatch filas
        if ($batchCount >= $insertBatch) {
            $sql_insert = "
                INSERT INTO productos (SKU, marca, estilo, color, genero, talla, statu, campana)
                VALUES " . implode(',', $values) . "
                ON DUPLICATE KEY UPDATE 
                    marca=VALUES(marca), estilo=VALUES(estilo), color=VALUES(color),
                    genero=VALUES(genero), talla=VALUES(talla),
                    statu=VALUES(statu), campana=VALUES(campana)
            ";
            $mysqli->query($sql_insert);
            $values = [];
            $batchCount = 0;
        }

        // 🔹 Mostrar progreso cada 10k filas
        if ($processed % 10000 == 0) {
            $percent = round(($processed / $total_rows) * 100, 2);
            $elapsed = round(microtime(true) - $start_time, 2);
            //echo "   → Procesados: $processed / $total_rows ($percent%) - {$elapsed}s\n";
        }
    }

    // 🟢 Insertar lo que quedó pendiente
    if (!empty($values)) {
        $sql_insert = "
            INSERT INTO productos (SKU, marca, estilo, color, genero, talla, statu, campana)
            VALUES " . implode(',', $values) . "
            ON DUPLICATE KEY UPDATE 
                marca=VALUES(marca), estilo=VALUES(estilo), color=VALUES(color),
                genero=VALUES(genero), talla=VALUES(talla),
                statu=VALUES(statu), campana=VALUES(campana)
        ";
        $mysqli->query($sql_insert);
    }

    // ✅ Confirmar bloque
    $mysqli->commit();
    sqlsrv_free_stmt($stmt_sqlsrv);

    $offset += $batchSize;

} while ($numRows == $batchSize);

// -------------------- FINALIZAR --------------------
$mysqli->query("SET unique_checks=1");
$mysqli->query("SET foreign_key_checks=1");
$mysqli->close();
sqlsrv_close($conn_sqlsrv);

$total_time = round(microtime(true) - $start_time, 2);
echo "\n✅ Importación completada correctamente.\n";
echo "   Registros procesados: $processed\n";
//echo "   Tiempo total: {$total_time} segundos\n";
//echo "===============================================\n";
?>
