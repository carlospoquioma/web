<?php
include("conexion.php");

$id = intval($_GET['id']);
$sql = "SELECT * FROM productos_observados WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "❌ Producto no encontrado";
    exit;
}

$producto = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<form id="formEditar" class="row g-2">
  <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">

  <div class="col-md-6">
    <label>SKU</label>
    <input type="text" name="sku" class="form-control" value="<?php echo $producto['sku']; ?>">
  </div>

  <div class="col-md-6">
    <label>Marca</label>
    <input type="text" name="marca" class="form-control" value="<?php echo $producto['marca']; ?>">
  </div>

  <div class="col-md-6">
    <label>Estilo</label>
    <input type="text" name="estilo" class="form-control" value="<?php echo $producto['estilo']; ?>">
  </div>

  <div class="col-md-6">
    <label>Color</label>
    <input type="text" name="color" class="form-control" value="<?php echo $producto['color']; ?>">
  </div>

  <div class="col-md-3">
    <label>Talla</label>
    <input type="text" name="talla" class="form-control" value="<?php echo $producto['talla']; ?>">
  </div>

  <div class="col-md-3">
    <label>Status</label>
    <input type="text" name="statu" class="form-control" value="<?php echo $producto['statu']; ?>">
  </div>

  <div class="col-md-6">
    <label>Campaña</label>
    <input type="text" name="campana" class="form-control" value="<?php echo $producto['campana']; ?>">
  </div>

  <div class="col-md-3">
    <label>Cantidad</label>
    <input type="number" name="cantidad" class="form-control" value="<?php echo $producto['cantidad']; ?>">
  </div>

  <div class="col-md-6">
    <label>Tipo de Falla</label>
    <input type="text" name="tipo_falla" class="form-control" value="<?php echo $producto['tipo_falla']; ?>">
  </div>

  <div class="col-md-6">
    <label>Fecha Recepción</label>
    <input type="date" name="fecha_recepcion" class="form-control" value="<?php echo $producto['fecha_recepcion']; ?>">
  </div>

  <div class="col-md-6">
    <label>N° Guía</label>
    <input type="text" name="nro_guia" class="form-control" value="<?php echo $producto['nro_guia']; ?>">
  </div>

  <div class="col-md-6">
    <label>Sucursal</label>
    <input type="text" name="sucursal" class="form-control" value="<?php echo $producto['sucursal']; ?>">
  </div>

  <div class="col-md-6">
    <label>Condición</label>
    <select name="condicion" class="form-select">
      <option value="aprobado" <?php if($producto['condicion']=='aprobado') echo "selected"; ?>>Aprobado</option>
      <option value="rechazado" <?php if($producto['condicion']=='rechazado') echo "selected"; ?>>Rechazado</option>
      <option value="en revision" <?php if($producto['condicion']=='en revision') echo "selected"; ?>>En Revisión</option>
    </select>
  </div>

 

  <div class="col-12 text-end mt-3">
    <button type="submit" class="btn btn-success">💾 Guardar cambios</button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
  </div>
</form>
