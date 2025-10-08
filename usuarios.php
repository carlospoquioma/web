<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['rol'] != 'administrador') {
    // Solo el admin puede gestionar usuarios
    header("Location: dashboard.php");
    exit();
}

include("conexion.php");

$nombre = $_SESSION['nombre'];
$rol    = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    </style>
</head>
<body>
    <!-- Navbar superior -->
    <div class="navbar d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-box-seam"></i> Inventario</span>
        <span><?php echo $nombre; ?> (<?php echo $rol; ?>)</span>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h6>NAVEGACIÓN PRINCIPAL</h6>
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>

        <?php if($rol == 'administrador'){ ?>
            <a href=""><i class="bi bi-people"></i> Clientes</a>
            <a href="productos.php"><i class="bi bi-box-seam"></i> Gestión de Productos</a>
            <a href="existencias.php"><i class="bi bi-truck"></i> Gestión de Existencias</a>
            <a href="usuarios.php" class="active"><i class="bi bi-person-badge"></i> Gestión de usuarios</a>
            <a href="reportes.php"><i class="bi bi-bar-chart"></i> Reportes</a>
            <a href=""><i class="bi bi-gear"></i> Configuración</a>
        <?php } elseif($rol == 'almacen'){ ?>
            <a href="productos.php"><i class="bi bi-box-seam"></i> Gestión de Productos</a>
        <?php } elseif($rol == 'cliente'){ ?>
            <a href="perfil.php"><i class="bi bi-person"></i> Mi Perfil</a>
            <a href="mis_compras.php"><i class="bi bi-cart"></i> Mis Compras</a>
        <?php } ?>

        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        
         <h3 class="mb-4 text-center">👨🏻‍💻 Gestión de Usuarios VIALE</h3>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalUsuario">
            <i class="bi bi-person-plus"></i> Nuevo Usuario
        </button>

        <!-- Tabla usuarios -->
        <div id="tablaUsuarios" class="table-responsive"></div>
    </div>

    <!-- Modal formulario usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formUsuario">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Registrar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_usuario" id="id_usuario">
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label>Contraseña</label>
                            <input type="password" class="form-control" name="password" id="password">
                        </div>
                        <div class="mb-3">
                            <label>Rol</label>
                            <select class="form-control" name="rol" id="rol" required>
                                <option value="administrador">Administrador</option>
                                <option value="almacen">Almacén</option>
                                <option value="cliente">Cliente</option>
                            </select>
                        </div>
                         <div class="mb-3">
                            <label>Sucursal</label>
                            <input type="sucursal" class="form-control" name="sucursal" id="sucursal">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function(){
        // Cargar tabla
        function cargarUsuarios(){
            $.get("usuarios_listar.php", function(data){
                $("#tablaUsuarios").html(data);
            });
        }
        cargarUsuarios();

        // Guardar/editar usuario
        $("#formUsuario").submit(function(e){
            e.preventDefault();
            $.post("usuarios_guardar.php", $(this).serialize(), function(resp){
                alert(resp);
                $("#modalUsuario").modal('hide');
                cargarUsuarios();
                $("#formUsuario")[0].reset();
            });
        });

        // Editar usuario
        $(document).on("click", ".editar", function(){
            var id = $(this).data("id");
            $.get("usuarios_editar.php", {id:id}, function(data){
                var usuario = JSON.parse(data);
                $("#id_usuario").val(usuario.id_usuario);
                $("#nombre").val(usuario.nombre);
                $("#email").val(usuario.email);
                $("#rol").val(usuario.rol);
                $("#modalUsuario").modal("show");
            });
        });

        // Eliminar usuario
        $(document).on("click", ".eliminar", function(){
            if(confirm("¿Seguro de eliminar?")){
                var id = $(this).data("id");
                $.post("usuarios_eliminar.php", {id:id}, function(resp){
                    alert(resp);
                    cargarUsuarios();
                });
            }
        });
    });
    </script>
</body>
</html>
