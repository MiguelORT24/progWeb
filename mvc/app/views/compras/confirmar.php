<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Confirmar Compra #<?php echo $data['compra']['id_compra']; ?></h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Al confirmar esta compra, se generarán automáticamente los lotes de inventario correspondientes.
                    </div>

                    <div class="mb-4">
                        <h5>Resumen</h5>
                        <p><strong>Proveedor:</strong> <?php echo $data['compra']['proveedor_nombre']; ?></p>
                        <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($data['compra']['fecha'])); ?></p>
                        <p><strong>Total:</strong> $<?php echo number_format($data['compra']['total'], 2); ?></p>
                    </div>

                    <div class="mb-4">
                        <h5>Detalle de Productos</h5>
                        <ul class="list-group">
                            <?php foreach($data['detalle'] as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $item['sku']; ?> - <?php echo $item['equipo_descripcion']; ?>
                                    <span class="badge bg-primary rounded-pill"><?php echo $item['cantidad']; ?> unid.</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <form action="<?php echo URLROOT; ?>/compras/confirmar/<?php echo $data['compra']['id_compra']; ?>" method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Ubicación de Recepción *</label>
                            <select name="id_ubicacion" class="form-select form-select-lg" required>
                                <option value="">Seleccione dónde se guardarán los lotes...</option>
                                <?php foreach($data['ubicaciones'] as $ubicacion): ?>
                                    <option value="<?php echo $ubicacion['id_ubicacion']; ?>">
                                        <?php echo $ubicacion['nombre']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Todos los productos de esta compra se ingresarán inicialmente en esta ubicación.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Confirmar y Generar Stock</button>
                            <a href="<?php echo URLROOT; ?>/compras" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
