<?php
include("conexion.php");

// Parámetros de paginación
$por_pagina = 10; // número de registros por página
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $por_pagina;

// Obtener total de registros
$total_query = $conn->query("SELECT COUNT(*) AS total FROM usuarios");
$total_registros = $total_query->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// Consulta principal con límite y orden
$sql = "SELECT * FROM usuarios ORDER BY id_usuario DESC LIMIT $inicio, $por_pagina";
$result = $conn->query($sql);
?>

<!-- ESTILOS -->
<style>
.table-container {
    max-height: 400px; /* Altura visible */
    overflow-y: auto; /* Barra vertical */
    border: 1px solid #ccc;
}

/* Encabezado fijo */
.table thead th {
    position: sticky;
    top: 0;
    background-color: #198754; /* Verde Bootstrap */
    color: white;
    z-index: 2;
}

/* Opcional: efecto visual */
.table-hover tbody tr:hover {
    background-color: #f1f1f1;
}

/* Paginación centrada */
.pagination {
    justify-content: center;
}
</style>

<!-- TABLA CON ENCABEZADO FIJO Y BARRA VERTICAL -->
<div class="table-container">
    <table class="table table-bordered table-hover">
        <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Sucursal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id_usuario']; ?></td>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo ucfirst($row['rol']); ?></td>
                <td><?php echo $row['sucursal']; ?></td>
                <td>
                    <button class="btn btn-sm btn-warning editar" data-id="<?php echo $row['id_usuario']; ?>">✏️ Editar</button>
                    <button class="btn btn-sm btn-danger eliminar" data-id="<?php echo $row['id_usuario']; ?>">🗑️ Eliminar</button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- PAGINACIÓN -->
<nav>
    <ul class="pagination mt-3">
        <?php if ($pagina > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>">&laquo; Anterior</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($pagina < $total_paginas): ?>
            <li class="page-item">
                <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>">Siguiente &raquo;</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
