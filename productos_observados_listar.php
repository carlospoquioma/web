<?php
session_start();
include("conexion.php");

$rol = $_SESSION['rol'] ?? "";
$sucursalUsuario = $_SESSION['sucursal'] ?? "";

// ----- Parámetros (GET) -----
$filtro        = $_GET['condicion'] ?? "todos";
$buscar        = trim($_GET['buscar'] ?? "");
$tipoBusqueda  = $_GET['tipoBusqueda'] ?? "ambos";
$pagina        = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;

// ----- Paginación -----
$registrosPorPagina = 10;
$offset = ($pagina - 1) * $registrosPorPagina;

// ----- Construcción de la query base -----
$sqlBase = "FROM productos_observados WHERE 1=1";

// filtro por rol
if ($rol === "almacen" && !empty($sucursalUsuario)) {
    $sqlBase .= " AND sucursal = '" . $conn->real_escape_string($sucursalUsuario) . "'";
}

// filtro por condición
if ($filtro !== "todos") {
    $sqlBase .= " AND condicion = '" . $conn->real_escape_string($filtro) . "'";
}

// filtro de búsqueda
if ($buscar !== "") {
    $buscarEsc = $conn->real_escape_string($buscar);
    if ($tipoBusqueda === "sku") {
        $sqlBase .= " AND sku LIKE '%$buscarEsc%'";
    } elseif ($tipoBusqueda === "estilo") {
        $sqlBase .= " AND estilo LIKE '%$buscarEsc%'";
    } else {
        $sqlBase .= " AND (sku LIKE '%$buscarEsc%' OR estilo LIKE '%$buscarEsc%')";
    }
}

// ----- Total registros -----
$sqlCount = "SELECT COUNT(*) AS total " . $sqlBase;
$resCount = $conn->query($sqlCount);
$totalRegistros = ($resCount && $resCount->num_rows > 0) ? (int)$resCount->fetch_assoc()['total'] : 0;
$totalPaginas = max(1, ceil($totalRegistros / $registrosPorPagina));

// ----- Consulta final -----
$sql = "SELECT * " . $sqlBase . " ORDER BY id DESC LIMIT $registrosPorPagina OFFSET $offset";
$result = $conn->query($sql);

// ----- Base URL -----
$params = [];
if ($buscar !== "")        $params['buscar'] = $buscar;
if ($tipoBusqueda !== "")  $params['tipoBusqueda'] = $tipoBusqueda;
if ($filtro !== "")        $params['condicion'] = $filtro;
$queryWithoutPage = http_build_query($params);
$script = htmlspecialchars($_SERVER['PHP_SELF']);
$baseUrl = $script . ($queryWithoutPage !== "" ? "?$queryWithoutPage&" : "?");
?>

<!-- ===== CONTENEDOR PRINCIPAL ===== -->
<div id="contenido-productos">

  <!-- ===== FORMULARIO DE FILTRO ===== -->
  <form id="form-filtro" class="row g-2 mb-3">
    <div class="col-auto">
      <input type="text" name="buscar" class="form-control" placeholder="Buscar SKU o Estilo" value="<?= htmlspecialchars($buscar) ?>">
    </div>
    <div class="col-auto">
      <select name="tipoBusqueda" class="form-control">
        <option value="ambos" <?= $tipoBusqueda === 'ambos' ? 'selected' : '' ?>>Ambos</option>
        <option value="sku" <?= $tipoBusqueda === 'sku' ? 'selected' : '' ?>>SKU</option>
        <option value="estilo" <?= $tipoBusqueda === 'estilo' ? 'selected' : '' ?>>Estilo</option>
      </select>
    </div>
    <div class="col-auto">
      <select name="condicion" class="form-control">
        <option value="todos" <?= $filtro === 'todos' ? 'selected' : '' ?>>Todos</option>
        <option value="aprobado" <?= $filtro === 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
        <option value="rechazado" <?= $filtro === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
        <option value="en revision" <?= $filtro === 'en revision' ? 'selected' : '' ?>>En revisión</option>
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-primary" type="submit">🔍 Filtrar</button>
    </div>
  </form>

  <!-- ===== INDICADOR DE CARGA ===== -->
  <div id="loader" class="text-center my-3" style="display:none;">
    <div class="spinner-border text-success" role="status"></div>
    <p class="mt-2 fw-bold text-success">Cargando datos...</p>
  </div>

  <!-- ===== TABLA ===== -->
  <div class="table-responsive" style="max-height: 500px; overflow-y: auto; border: 2px solid #dee2e6;">
    <table class="table table-bordered table-hover align-middle table-sm">
      <thead class="table-success text-center sticky-top" style="top: 0; z-index:2;">
        <tr>
          <th>ID</th><th>SKU</th><th>Marca</th><th>Estilo</th><th>Color</th><th>Talla</th>
          <th>Status</th><th>Campaña</th><th>Cantidad</th><th>Tipo Falla</th><th>Fecha Registro</th>
          <th>Nro Guía</th><th>Sucursal</th><th>Foto</th><th>Condición</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <?php
              if ($row['condicion'] === 'aprobado') $classFila = 'table-success';
              elseif ($row['condicion'] === 'rechazado') $classFila = 'table-danger';
              else $classFila = 'table-warning';
            ?>
            <tr class="<?= $classFila ?>">
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['sku']) ?></td>
              <td><?= htmlspecialchars($row['marca']) ?></td>
              <td><?= htmlspecialchars($row['estilo']) ?></td>
              <td><?= htmlspecialchars($row['color']) ?></td>
              <td><?= htmlspecialchars($row['talla']) ?></td>
              <td><?= htmlspecialchars($row['statu']) ?></td>
              <td><?= htmlspecialchars($row['campana']) ?></td>
              <td><?= htmlspecialchars($row['cantidad']) ?></td>
              <td><?= htmlspecialchars($row['tipo_falla']) ?></td>
              <td><?= htmlspecialchars($row['fecha_registro']) ?></td>
              <td><?= htmlspecialchars($row['nro_guia']) ?></td>
              <td><?= htmlspecialchars($row['sucursal']) ?></td>
              <td>
                <?php if (!empty($row['foto'])): ?>
                  <a href="<?= htmlspecialchars($row['foto']) ?>" target="_blank">
                    <img src="<?= htmlspecialchars($row['foto']) ?>" class="img-thumbnail" style="max-height:40px;">
                  </a>
                <?php else: ?>
                  <span class="text-muted">Sin foto</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($row['condicion'] === 'aprobado'): ?>
                  <span class="badge bg-success">Aprobado</span>
                <?php elseif ($row['condicion'] === 'rechazado'): ?>
                  <span class="badge bg-danger">Rechazado</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">En revisión</span>
                <?php endif; ?>
              </td>
             <td class="text-center">
              <?php if ($rol === 'administrador'): ?>
                <button class="btn btn-sm btn-warning editar" data-id="<?= $row['id'] ?>">✏️</button>
                <button class="btn btn-sm btn-danger eliminar" data-id="<?= $row['id'] ?>">🗑️</button>
                <?php if ($row['condicion'] === 'aprobado'): ?>
                  <button class="btn btn-sm btn-success imprimir" data-id="<?= $row['id'] ?>">🖨️</button>
                <?php endif; ?>
              <?php else: ?>
                <?php if ($row['condicion'] === 'aprobado'): ?>
                  <button class="btn btn-sm btn-success imprimir" data-id="<?= $row['id'] ?>">🖨️</button>
                <?php else: ?>
                  <span class="text-muted small">Sin acciones</span>
                <?php endif; ?>
              <?php endif; ?>
            </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="16" class="text-center text-muted">No se encontraron productos</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- ===== PAGINACIÓN ===== -->
  <?php if ($totalPaginas > 1): ?>
  <nav>
    <ul class="pagination justify-content-center mt-3">
      <li class="page-item <?= ($pagina <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= $baseUrl ?>pagina=<?= max(1, $pagina - 1) ?>">«Anterior</a>
      </li>
      <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <li class="page-item <?= ($i === $pagina) ? 'active' : '' ?>">
          <a class="page-link" href="<?= $baseUrl ?>pagina=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= ($pagina >= $totalPaginas) ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= $baseUrl ?>pagina=<?= min($totalPaginas, $pagina + 1) ?>">Siguiente»</a>
      </li>
    </ul>
  </nav>
  <?php endif; ?>
</div>

<?php $conn->close(); ?>

<!-- ===== SCRIPT AJAX + LOADER ===== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
  // Mostrar loader mientras se cargan datos
  function mostrarLoader(mostrar) {
    if (mostrar) {
      $("#loader").fadeIn(200);
    } else {
      $("#loader").fadeOut(200);
    }
  }

  // Paginación AJAX
  $(document).on('click', '.pagination a', function(e){
    e.preventDefault();
    const url = $(this).attr('href');
    mostrarLoader(true);
    $("#contenido-productos").load(url + " #contenido-productos>*", function(){
      mostrarLoader(false);
    });
  });

  // Filtro/búsqueda AJAX
  $("#form-filtro").on("submit", function(e){
    e.preventDefault();
    const datos = $(this).serialize();
    const url = "<?= $_SERVER['PHP_SELF'] ?>?" + datos;
    mostrarLoader(true);
    $("#contenido-productos").load(url + " #contenido-productos>*", function(){
      mostrarLoader(false);
    });
  });
});
</script>


<!--------------------------------------------- para la tabla -->


<!---------------------------------------------  -->

<!-- Modal Bootstrap -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- Aquí se cargará el formulario vía AJAX -->
      </div>
    </div>
  </div>
</div>

<!-- JQuery + Bootstrap JS (si no los tienes ya incluidos) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).on("click", ".editar", function(){
    let id = $(this).data("id");

    $.get("editar_form_ajax.php", {id:id}, function(html){
        $("#modalBody").html(html);
        let modal = new bootstrap.Modal(document.getElementById('modalEditar'));
        modal.show();
    });
});

// Guardar cambios con AJAX
$(document).on("submit", "#formEditar", function(e){
    e.preventDefault();
    $.post("productos_editar.php", $(this).serialize(), function(resp){
        if(resp.trim() === "ok"){
            location.reload(); // refresca la tabla
        } else {
            alert("Error: " + resp);
        }
    });
});

$(document).on("click", ".imprimir", function(){
    let fila = $(this).closest("tr");

    // Extraer valores de las celdas
    let sku         = fila.find("td:eq(1)").text();
    let marca       = fila.find("td:eq(2)").text();
    let estilo      = fila.find("td:eq(3)").text();
    let color       = fila.find("td:eq(4)").text();
    let talla       = fila.find("td:eq(5)").text();
    let statu       = fila.find("td:eq(6)").text();
    let campana     = fila.find("td:eq(7)").text();
    let cantidad    = fila.find("td:eq(8)").text();
    let tipo_falla  = fila.find("td:eq(9)").text();
    let fecha_reg   = fila.find("td:eq(10)").text();
    let nro_guia    = fila.find("td:eq(11)").text();
    let sucursal    = fila.find("td:eq(12)").text();
    let foto        = fila.find("td:eq(13)").html(); // conserva imagen
    let condicion   = fila.find("td:eq(14)").text();

    // Construir tabla personalizada
    let tabla = `
        <table class="table table-bordered">
            <tr><th>SKU</th><td>${sku}</td></tr>
            <tr><th>Marca</th><td>${marca}</td></tr>
            <tr><th>Estilo</th><td>${estilo}</td></tr>
            <tr><th>Color</th><td>${color}</td></tr>
            <tr><th>Talla</th><td>${talla}</td></tr>
            <tr><th>Status</th><td>${statu}</td></tr>
            <tr><th>Campaña</th><td>${campana}</td></tr>
            <tr><th>Cantidad</th><td>${cantidad}</td></tr>
            <tr><th>Tipo Falla</th><td>${tipo_falla}</td></tr>
            <tr><th>Fecha Registro</th><td>${fecha_reg}</td></tr>
            <tr><th>Nro</th><td>${nro_guia}</td></tr>
            <tr><th>Sucursal</th><td>${sucursal}</td></tr>
            <tr><th>Foto</th><td>${foto}</td></tr>
            <tr><th>Condición</th><td>${condicion}</td></tr>
        </table>
    `;

    // Crear ventana emergente para imprimir
    let ventana = window.open("", "Imprimir", "width=900,height=600");
    ventana.document.write("<html><head><title>Imprimir Producto</title>");
    ventana.document.write("<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>");
    ventana.document.write("</head><body class='p-4'>");

    // 🔹 Agregar logo
    ventana.document.write(`
        <div class="text-center mb-3">
            <img src="uploads/logo.jpg" alt="Logo" style="max-height:80px;">
        </div>
    `);

    ventana.document.write("<h3 class='text-center mb-4'>Registros de Productos Observados</h3>");
    ventana.document.write(tabla);
    ventana.document.write("</body></html>");
    ventana.document.close();
    ventana.print();
});

</script>
