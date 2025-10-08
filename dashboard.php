<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

include("conexion.php");

$nombre = $_SESSION['nombre'];
$rol    = $_SESSION['rol'];

// Totales para dashboard
$totalUsuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
/*$totalProductos = $conn->query("SELECT COUNT(*) AS total FROM productos")->fetch_assoc()['total'];*/
/*$totalClientes  = $conn->query("SELECT COUNT(*) AS total FROM clientes")->fetch_assoc()['total'];*/
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; margin:0; font-family: Arial, sans-serif; }
        .navbar {
            background: #00897B; /* verde cabecera */
            padding: 12px 20px;
            color: white;
            font-size: 18px;
        }
        .sidebar {
            width: 240px;
            height: 100vh;
            background: #fff;
            border-right: 1px solid #ddd;
            position: fixed;
            top: 50px;
            left: 0;
            padding: 20px 10px;
        }
        .sidebar h6 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: #555;
        }
        .sidebar a {
            display: block;
            padding: 10px 15px;
            margin: 5px 0;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            font-size: 15px;
        }
        .sidebar a:hover {
            background: #e0f2f1;
            color: #00897B;
        }
        .sidebar a.active {
            background: #00897B;
            color: #fff;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            margin-top: 60px;
        }
        .card-icon {
            font-size: 40px;
            color: #00897B;
        }
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
        <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>

        <?php if($rol == 'administrador'){ ?>
            <a href=""><i class="bi bi-people"></i> Clientes</a>
            <a href="productos.php"><i class="bi bi-box-seam"></i> Gestión de Productos</a>
            <a href="existencias.php"><i class="bi bi-truck"></i> Gestión de Existencias</a>
            <a href="usuarios.php"><i class="bi bi-person-badge"></i> Gestión de usuarios</a>
            <a href="reportes.php"><i class="bi bi-bar-chart"></i> Reportes</a>
            <a href=""><i class="bi bi-gear"></i> Configuración</a>
        <?php } elseif($rol == 'almacen'){ ?>
            <a href="productos.php"><i class="bi bi-box-seam"></i> Gestión de Productos</a>
        <?php } elseif($rol == 'cliente'){ ?>
            <a href="productos.php"><i class="bi bi-person"></i> Gestión de Productos</a>
            <a href="reportes.php"><i class="bi bi-cart"></i> Reportes</a>
        <?php } ?>

        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <h3>📊 Dashboard</h3>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="card-icon me-3"><i class="bi bi-boxes"></i></div>
                        <div>
                          
                            <small>Productos</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($rol == 'administrador'){ ?>
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="card-icon me-3"><i class="bi bi-people"></i></div>
                        <div>
                            
                            <small>Clientes</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="card-icon me-3"><i class="bi bi-person-badge"></i></div>
                        <div>
                            <h5><?php echo $totalUsuarios; ?></h5>
                            <small>Usuarios</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
