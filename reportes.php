<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

include("conexion.php");

$nombre = $_SESSION['nombre'];
$rol = $_SESSION['rol'];

// Detectar la página actual
$pagina_actual = basename($_SERVER['PHP_SELF']);

// Validar fecha
function valid_date($d){
    $t = DateTime::createFromFormat('Y-m-d', $d);
    return $t && $t->format('Y-m-d') === $d;
}

$fecha_inicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : "";
$fecha_fin = isset($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : "";

// --- Paginación ---
$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$inicio = ($pagina - 1) * $por_pagina;

// --- Filtro de fechas ---
$where = "";
$params = [];
$types = "";

if ($fecha_inicio !== "" && $fecha_fin !== "" && valid_date($fecha_inicio) && valid_date($fecha_fin)) {
    $where = "WHERE fecha_registro BETWEEN ? AND ?";
    $params = [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'];
    $types = 'ss';
}

// --- Total de registros ---
$total_registros = 0;
if ($where !== "") {
    $sql_total = "SELECT COUNT(*) AS total FROM productos_observados $where";
    $stmt_total = $conn->prepare($sql_total);
    if ($stmt_total) {
        $bind = array_merge([$types], $params);
        $bind_names = [];
        $bind_names[] = $types;
        for ($i = 0; $i < count($params); $i++) $bind_names[] = &$params[$i];
        call_user_func_array([$stmt_total,'bind_param'], $bind_names);
        $stmt_total->execute();
        $res_total = $stmt_total->get_result();
        $total_registros = (int)($res_total->fetch_assoc()['total'] ?? 0);
        $stmt_total->close();
    }
} else {
    $res_total = $conn->query("SELECT COUNT(*) AS total FROM productos_observados");
    $total_registros = $res_total ? (int)($res_total->fetch_assoc()['total'] ?? 0) : 0;
}
$total_paginas = $total_registros > 0 ? ceil($total_registros / $por_pagina) : 1;

// --- Consulta principal ---
$sql = "SELECT * FROM productos_observados $where ORDER BY id DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$result = false;
if ($stmt) {
    $types_main = $types . 'ii';
    $params_main = $params;
    $params_main[] = $inicio;
    $params_main[] = $por_pagina;
    $bind_names = [];
    $bind_names[] = $types_main;
    for ($i = 0; $i < count($params_main); $i++) $bind_names[] = &$params_main[$i];
    call_user_func_array([$stmt, 'bind_param'], $bind_names);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM productos_observados ORDER BY id DESC LIMIT $inicio, $por_pagina");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background-color: #f4f6f9; margin:0; font-family: Arial, sans-serif; }
        .navbar { background: #00897B; padding: 12px 20px; color: white; font-size: 18px; }
        .sidebar { width: 240px; height: 100vh; background: #fff; border-right: 1px solid #ddd;
                   position: fixed; top: 50px; left: 0; padding: 20px 10px; }
        .sidebar h6 { text-align: center; font-weight: bold; margin-bottom: 20px; color: #555; }
        .sidebar a { display: block; padding: 10px 15px; margin: 5px 0; color: #333;
                     text-decoration: none; border-radius: 5px; font-size: 15px; }
        .sidebar a:hover { background: #e0f2f1; color: #00897B; }
        .sidebar a.active { background: #00897B; color: #fff; font-weight: bold; }
        .content { margin-left: 250px; padding: 20px; margin-top: 60px; }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar d-flex justify-content-between align-items-center">
    <span class="fw-bold"><i class="bi bi-box-seam"></i> Inventario</span>
    <span><?php echo $nombre; ?> (<?php echo $rol; ?>)</span>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <h6>NAVEGACIÓN PRINCIPAL</h6>
    <a href="dashboard.php" class="<?= ($pagina_actual == 'dashboard.php') ? 'active' : ''; ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>

    <?php if($rol == 'administrador'){ ?>
        <a href="" class="<?= ($pagina_actual == '') ? 'active' : ''; ?>"><i class="bi bi-people"></i> Clientes</a>
        <a href="productos.php" class="<?= ($pagina_actual == 'productos.php') ? 'active' : ''; ?>"><i class="bi bi-box-seam"></i> Gestión de Productos</a>
        <a href="existencias.php" class="<?= ($pagina_actual == 'existencias.php') ? 'active' : ''; ?>"><i class="bi bi-truck"></i> Gestión de Existencias</a>
        <a href="usuarios.php" class="<?= ($pagina_actual == 'usuarios.php') ? 'active' : ''; ?>"><i class="bi bi-person-badge"></i> Gestión de usuarios</a>
        <a href="reportes.php" class="<?= ($pagina_actual == 'reportes.php') ? 'active' : ''; ?>"><i class="bi bi-bar-chart"></i> Reportes</a>
        <a href="" class="<?= ($pagina_actual == '') ? 'active' : ''; ?>"><i class="bi bi-gear"></i> Configuración</a>
    <?php } elseif($rol == 'almacen'){ ?>
        <a href="productos.php" class="<?= ($pagina_actual == 'productos.php') ? 'active' : ''; ?>"><i class="bi bi-box-seam"></i> Gestión de Productos</a>
    <?php } elseif($rol == 'cliente'){ ?>
       <a href="productos.php" class="<?= ($pagina_actual == 'productos.php') ? 'active' : ''; ?>"><i class="bi bi-box-seam"></i> Gestión de Productos</a>
        <a href="reportes.php" class="<?= ($pagina_actual == 'reportes.php') ? 'active' : ''; ?>"><i class="bi bi-bar-chart"></i> Reportes</a>
    <?php } ?>

    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a>
</div>

<!-- Contenido principal -->
<div class="content">
    <div class="container-fluid">
        <h3 class="mb-4 text-center">📅 Reporte de Productos Observados</h3>

        <!-- FILTRO POR FECHAS -->
        <form method="GET" class="row g-3 mb-4 justify-content-center">
            <div class="col-auto">
                <label class="form-label">Desde:</label>
                <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio); ?>" class="form-control" required>
            </div>
            <div class="col-auto">
                <label class="form-label">Hasta:</label>
                <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin); ?>" class="form-control" required>
            </div>
            <div class="col-auto align-self-end">
                <button class="btn btn-success" type="submit"><i class="bi bi-search"></i> Filtrar</button>
            </div>
            <?php if ($fecha_inicio && $fecha_fin): ?>
            <div class="col-auto align-self-end">
                <a href="exportar_excel.php?fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
                   class="btn btn-outline-success">
                   <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
                </a>
            </div>
            <?php endif; ?>
        </form>

        <?php if ($result === false): ?>
            <div class="alert alert-danger">Ocurrió un error al consultar la base de datos.</div>
        <?php else: ?>
            <div class="table-container mt-3">
                <table class="table table-bordered table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>ID</th><th>SKU</th><th>Marca</th><th>Estilo</th><th>Color</th>
                            <th>Talla</th><th>Status</th><th>Campaña</th><th>Cantidad</th>
                            <th>Tipo Falla</th><th>Fecha Registro</th><th>Nro Guía</th>
                            <th>Sucursal</th><th>Condición</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows === 0): ?>
                            <tr><td colspan="14" class="text-center">No hay registros para las fechas seleccionadas.</td></tr>
                        <?php else: ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']); ?></td>
                                <td><?= htmlspecialchars($row['sku']); ?></td>
                                <td><?= htmlspecialchars($row['marca']); ?></td>
                                <td><?= htmlspecialchars($row['estilo']); ?></td>
                                <td><?= htmlspecialchars($row['color']); ?></td>
                                <td><?= htmlspecialchars($row['talla']); ?></td>
                                <td><?= htmlspecialchars($row['statu']); ?></td>
                                <td><?= htmlspecialchars($row['campana']); ?></td>
                                <td><?= htmlspecialchars($row['cantidad']); ?></td>
                                <td><?= htmlspecialchars($row['tipo_falla']); ?></td>
                                <td><?= htmlspecialchars($row['fecha_registro']); ?></td>
                                <td><?= htmlspecialchars($row['nro_guia']); ?></td>
                                <td><?= htmlspecialchars($row['sucursal']); ?></td>
                                <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($row['condicion']); ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINACIÓN -->
            <nav>
                <ul class="pagination mt-3 justify-content-center">
                    <?php if ($pagina > 1): ?>
                        <li class="page-item"><a class="page-link" href="?pagina=<?= $pagina - 1; ?>&fecha_inicio=<?= $fecha_inicio; ?>&fecha_fin=<?= $fecha_fin; ?>">&laquo; Anterior</a></li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?= ($i == $pagina) ? 'active' : ''; ?>">
                            <a class="page-link" href="?pagina=<?= $i; ?>&fecha_inicio=<?= $fecha_inicio; ?>&fecha_fin=<?= $fecha_fin; ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($pagina < $total_paginas): ?>
                        <li class="page-item"><a class="page-link" href="?pagina=<?= $pagina + 1; ?>&fecha_inicio=<?= $fecha_inicio; ?>&fecha_fin=<?= $fecha_fin; ?>">Siguiente &raquo;</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
