<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="bi bi-arrow-up-circle"></i> Registrar Nueva Salida</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['error'])): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($data['error'] as $err): ?>
                                <div><?php echo $err; ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo URLROOT; ?>/movimientos/<?php echo $data['tipo']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Lote *</label>
                            <select name="id_lote" class="form-select" required>
                                <option value="">Seleccione un lote...</option>
                                <?php foreach ($data['lotes'] as $lote): ?>
                                    <option value="<?php echo $lote['id_lote']; ?>" <?php echo ($data['id_lote'] ?? '') == $lote['id_lote'] ? 'selected' : ''; ?>>
                                        #<?php echo $lote['id_lote']; ?> | SKU: <?php echo $lote['sku']; ?> | <?php echo $lote['ubicacion_nombre']; ?> | Cant: <?php echo $lote['cantidad']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cantidad *</label>
                                <input type="number" name="cantidad" class="form-control" min="1" required value="<?php echo $data['cantidad'] ?? ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Motivo (opcional)</label>
                                <input type="text" name="motivo" class="form-control" value="<?php echo $data['motivo'] ?? ''; ?>" placeholder="Ej: salida a cliente, ajuste">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha y Hora</label>
                                <input type="datetime-local" class="form-control" value="<?php echo date('Y-m-d\\TH:i'); ?>" readonly>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo URLROOT; ?>/movimientos" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-warning text-dark"><i class="bi bi-check2-circle"></i> Guardar salida</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
