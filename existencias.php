<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'administrador') {
    header("Location: dashboard.php");
    exit();
}
$nombre = $_SESSION['nombre'] ?? 'Usuario';
$rol    = $_SESSION['rol'] ?? 'cliente';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Importar Productos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body { background-color: #f4f6f9; margin:0; font-family: Arial, sans-serif; }
.navbar { background: #00897B; padding: 12px 20px; color: white; font-size: 18px; }
.sidebar { width: 240px; height: 100vh; background: #fff; border-right: 1px solid #ddd; position: fixed; top: 50px; left: 0; padding: 20px 10px; }
.sidebar h6 { text-align: center; font-weight: bold; margin-bottom: 20px; color: #555; }
.sidebar a { display: block; padding: 10px 15px; margin: 5px 0; color: #333; text-decoration: none; border-radius: 5px; font-size: 15px; }
.sidebar a:hover { background: #e0f2f1; color: #00897B; }
.sidebar a.active { background: #00897B; color: #fff; }
.content { margin-left: 250px; padding: 20px; margin-top: 60px; }
.progress { height: 30px; }
</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar d-flex justify-content-between align-items-center">
    <span class="fw-bold"><i class="bi bi-box-seam"></i> Inventario</span>
    <span><?php echo $nombre; ?> (<?php echo $rol; ?>)</span>
</div>

<!-- Sidebar -->
<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<div class="sidebar">
    <h6>NAVEGACIÓN PRINCIPAL</h6>

    <a href="dashboard.php" <?php if($current_page=='dashboard.php') echo 'class="active"'; ?>>
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <?php if($rol == 'administrador'){ ?>
        <a href="clientes.php" <?php if($current_page=='clientes.php') echo 'class="active"'; ?>>
            <i class="bi bi-people"></i> Clientes
        </a>
        <a href="productos.php" <?php if($current_page=='productos.php') echo 'class="active"'; ?>>
            <i class="bi bi-box-seam"></i> Gestión de Productos
        </a>
        <a href="existencias.php" <?php if($current_page=='existencias.php') echo 'class="active"'; ?>>
            <i class="bi bi-truck"></i> Gestión de Existencias
        </a>
        <a href="usuarios.php" <?php if($current_page=='usuarios.php') echo 'class="active"'; ?>>
            <i class="bi bi-person-badge"></i> Gestión de usuarios
        </a>
        <a href="reportes.php" <?php if($current_page=='reportes.php') echo 'class="active"'; ?>>
            <i class="bi bi-bar-chart"></i> Reportes
        </a>
        <a href="configuracion.php" <?php if($current_page=='configuracion.php') echo 'class="active"'; ?>>
            <i class="bi bi-gear"></i> Configuración
        </a>
    <?php } elseif($rol == 'almacen'){ ?>
        <a href="productos.php" <?php if($current_page=='productos.php') echo 'class="active"'; ?>>
            <i class="bi bi-box-seam"></i> Gestión de Productos
        </a>
        <a href="existencias.php" <?php if($current_page=='existencias.php') echo 'class="active"'; ?>>
            <i class="bi bi-truck"></i> Gestión de Existencias
        </a>
    <?php } elseif($rol == 'cliente'){ ?>
        <a href="perfil.php" <?php if($current_page=='perfil.php') echo 'class="active"'; ?>>
            <i class="bi bi-person"></i> Mi Perfil
        </a>
        <a href="mis_compras.php" <?php if($current_page=='mis_compras.php') echo 'class="active"'; ?>>
            <i class="bi bi-cart"></i> Mis Compras
        </a>
    <?php } ?>

    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a>
</div>


<!-- Contenido -->
<div class="content">
    <h3 class="mb-4 text-center">📦 Importación de Productos desde SQL Server</h3>

    <!-- Botón para iniciar importación -->
    <div class="text-center mb-3">
        <button id="btnActualizar" class="btn btn-success btn-lg">
            <i class="bi bi-arrow-repeat"></i> Actualizar Productos
        </button>
    </div>

    <!-- Barra de progreso -->
    <div class="progress mb-3">
        <div id="progressBar" class="progress-bar" role="progressbar" style="width:0%;">0%</div>
    </div>
    <div id="info" class="mb-3"></div>
</div>

<script>
$(document).ready(function(){
    $('#btnActualizar').click(function(){
        $(this).prop('disabled', true).text('Importando...');
        $('#info').html('Iniciando importación...');
        
        $.ajax({
            url: 'importar_desde_sqlserver.php', // tu script que hace la importación
            type: 'GET',
            success: function(data){
                $('#info').html(data); // mostrar mensajes de importación
                $('#btnActualizar').prop('disabled', false).text('Actualizar Productos');
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
