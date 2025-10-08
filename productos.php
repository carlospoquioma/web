<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
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
    <title>Gestión de Productos</title>
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
        .sidebar a.active { background: #00897B; color: #fff; }
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
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <?php if($rol == 'administrador'){ ?>
            <a href=""><i class="bi bi-people"></i> Clientes</a>
            <a href="productos.php" class="active"><i class="bi bi-box-seam"></i> Gestión de Productos</a>
            <a href="existencias.php"><i class="bi bi-truck"></i> Gestión de Existencias</a>
            <a href="usuarios.php"><i class="bi bi-person-badge"></i> Gestión de usuarios</a>
            <a href="reportes.php"><i class="bi bi-bar-chart"></i> Reportes</a>
            <a href=""><i class="bi bi-gear"></i> Configuración</a>
        <?php } elseif($rol == 'almacen'){ ?>
            <a href="productos.php" class="active"><i class="bi bi-box-seam"></i> Gestión de Productos</a>
        <?php } elseif($rol == 'cliente'){ ?>
            <a href="productos.php" class="active"><i class="bi bi-box-seam"></i> Gestión de Productos</a>
           <a href="reportes.php"><i class="bi bi-bar-chart"></i> Reportes</a>
        <?php } ?>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a>
    </div>

   <!-- Contenido -->
<div class="content">

   <h3 class="mb-4 text-center">📦 Gestión de Productos Observados VIALE</h3>
  <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalProducto">
    <i class="bi bi-plus-circle"></i> Nuevo Producto
  </button>

  <!-- Tabla productos -->
  <div id="tablaProductos" class="table-responsive"></div>
</div>

<!-- Modal Registrar Producto Observado -->
<div class="modal fade" id="modalProducto" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form id="formProducto" enctype="multipart/form-data">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Registrar Producto Observado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <!-- 🔍 BUSCADOR DE PRODUCTOS -->
          <div class="mb-3 p-3 border rounded bg-light">
            <h6><i class="bi bi-search"></i> Buscar producto por SKU o Estilo</h6>
            <div class="d-flex mb-2">
              <input type="text" id="buscarProducto" class="form-control me-2" placeholder="Ingrese SKU o Estilo">
              <button type="button" id="btnBuscarProducto" class="btn btn-primary">Buscar</button>
            </div>

            <!-- Tabla de resultados -->
            <div id="tablaBusqueda" class="table-responsive" style="max-height: 250px; overflow-y:auto; display:none;">
              <table class="table table-bordered table-hover align-middle table-sm">
                <thead class="table-secondary text-center sticky-top">
                  <tr>
                    <th>SKU</th>
                    <th>Marca</th>
                    <th>Estilo</th>
                    <th>Color</th>
                    <th>Talla</th>
                    <th>Status</th>
                    <th>Campaña</th>
                    <th>Seleccionar</th>
                  </tr>
                </thead>
                <tbody id="resultadoBusqueda"></tbody>
              </table>
            </div>
          </div>

          <!-- 📝 FORMULARIO PRINCIPAL -->
          <div class="row g-2">
            <input type="hidden" name="id">
            <div class="col-md-6">
              <label>SKU</label>
              <input type="text" name="sku" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label>Marca</label>
              <input type="text" name="marca" class="form-control">
            </div>
            <div class="col-md-6">
              <label>Estilo</label>
              <input type="text" name="estilo" class="form-control">
            </div>
            <div class="col-md-6">
              <label>Color</label>
              <input type="text" name="color" class="form-control">
            </div>
            <div class="col-md-3">
              <label>Talla</label>
              <input type="text" name="talla" class="form-control">
            </div>
            <div class="col-md-3">
              <label>Status</label>
              <input type="text" name="statu" class="form-control">
            </div>
            <div class="col-md-6">
              <label>Campaña</label>
              <input type="text" name="campana" class="form-control">
            </div>
            <div class="col-md-3">
              <label>Cantidad</label>
              <input type="number" name="cantidad" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label>Tipo de falla</label>
              <input type="text" name="tipo_falla" class="form-control">
            </div>
            <div class="col-md-6">
              <label>Fecha recepción</label>
              <input type="date" name="fecha_recepcion" class="form-control">
            </div>
            <div class="col-md-6">
              <label>N° Guía</label>
              <input type="text" name="nro_guia" class="form-control">
            </div>
             <?php if($rol=="administrador"){ ?>
            <div class="col-md-6">
              <label>Sucursal</label>
              <input type="text" name="sucursal" class="form-control">
            </div>
           
            <div class="col-md-6">
              <label>Condición</label>
              <select name="condicion" class="form-control">
                <option value="en revision">En Revisión</option>
                <option value="aprobado">Aprobado</option>
                <option value="rechazado">Rechazado</option>
              </select>
            </div>
            <?php } ?>
            <div class="col-md-12">
              <label>Foto</label>
              <input type="file" name="foto" class="form-control" accept="image/*" capture="environment">
            </div>
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
        function cargarProductos(){
            $.get("productos_observados_listar.php", function(data){
                $("#tablaProductos").html(data);
            });
        }
        cargarProductos();

        // Guardar producto
       /* $("#formProducto").submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "productos_observados_guardar.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp){
                    alert(resp);
                    $("#modalProducto").modal('hide');
                    cargarProductos();
                    $("#formProducto")[0].reset();
                }
            });
        });*/
        $("#formProducto").on("submit", function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: "productos_observados_guardar.php",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(resp) {
            if (resp.trim() === "ok") {
                alert("✅ Guardado correctamente");
                $("#modalProducto").modal("hide");
                // refrescar tabla
                location.reload(); 
            } else {
                alert("❌ Error: " + resp);
            }
        },
        error: function(xhr, status, error) {
            alert("Error AJAX: " + error);
        }
    });
});



      

        // Eliminar
        $(document).on("click", ".eliminar", function(){
            if(confirm("¿Eliminar producto?")){
                var id = $(this).data("id");
                $.post("eliminar_producto.php", {id:id}, function(resp){
                    alert(resp);
                    cargarProductos();
                });
            }
        });
    });



    $(document).ready(function() {
  // 🔍 Buscar productos
  $('#btnBuscarProducto').on('click', function() {
    let q = $('#buscarProducto').val().trim();
    if (q === '') return;

    $.get('buscar_producto.php', { q: q }, function(data) {
      $('#resultadoBusqueda').html(data);
      $('#tablaBusqueda').show();
    });
  });

  // ⏎ Buscar al presionar Enter
  $('#buscarProducto').on('keypress', function(e) {
    if (e.which === 13) {
      e.preventDefault();
      $('#btnBuscarProducto').click();
    }
  });

  // 🟢 Seleccionar producto y llenar formulario
  $(document).on('click', '.seleccionar', function() {
    $('input[name="sku"]').val($(this).data('sku'));
    $('input[name="marca"]').val($(this).data('marca'));
    $('input[name="estilo"]').val($(this).data('estilo'));
    $('input[name="color"]').val($(this).data('color'));
    $('input[name="talla"]').val($(this).data('talla'));
    $('input[name="statu"]').val($(this).data('statu'));
    $('input[name="campana"]').val($(this).data('campana'));
    $('#tablaBusqueda').hide();
    $('#buscarProducto').val('');
  });
});
    </script>

    
</body>
</html>
