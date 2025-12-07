<?php require_once APPROOT . '/views/layouts/header.inc.php'; ?>

<h2><i class="fa fa-arrow-up"></i> Registrar Salida de Inventario</h2>

<?php if(!empty($data['error'])): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach($data['error'] as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label for="producto_id" class="form-label">Producto *</label>
        <select class="form-select" id="producto_id" name="producto_id" required>
            <option value="">Seleccione un producto</option>
            <?php foreach($data['productos'] as $prod): ?>
                <option value="<?= $prod['id'] ?>" <?= (isset($data['producto_id']) && $data['producto_id'] == $prod['id']) ? 'selected' : '' ?>>
                    <?= $prod['producto_codigo'] ?> - <?= $prod['producto_nombre'] ?> (Stock: <?= $prod['producto_stock'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="movimiento_cantidad" class="form-label">Cantidad *</label>
                <input type="number" class="form-control" id="movimiento_cantidad" name="movimiento_cantidad" 
                       value="<?= $data['movimiento_cantidad'] ?? '' ?>" required min="1">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="movimiento_precio_unitario" class="form-label">Precio Unitario *</label>
                <input type="number" step="0.01" class="form-control" id="movimiento_precio_unitario" name="movimiento_precio_unitario" 
                       value="<?= $data['movimiento_precio_unitario'] ?? '' ?>" required min="0.01">
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="movimiento_motivo" class="form-label">Motivo</label>
        <input type="text" class="form-control" id="movimiento_motivo" name="movimiento_motivo" 
               value="<?= $data['movimiento_motivo'] ?? '' ?>" placeholder="Ej: Venta, Merma, Devolución, etc.">
    </div>

    <div class="alert alert-warning">
        <i class="fa fa-exclamation-triangle"></i> Asegúrese de que hay stock suficiente antes de registrar la salida.
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Registrar Salida</button>
        <a href="<?= URLROOT ?>/movimientos" class="btn btn-secondary"><i class="fa fa-times"></i> Cancelar</a>
    </div>
</form>

<?php require_once APPROOT . '/views/layouts/footer.inc.php'; ?>
