<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Registrar Nuevo Lote</h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/inventario/crear" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Equipo *</label>
                            <select name="id_equipo" class="form-select" required>
                                <option value="">Seleccione un equipo...</option>
                                <?php foreach($data['equipos'] as $equipo): ?>
                                    <option value="<?php echo $equipo['id_equipo']; ?>">
                                        <?php echo $equipo['sku'] . ' - ' . $equipo['descripcion']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ubicación *</label>
                                <select name="id_ubicacion" class="form-select" required>
                                    <option value="">Seleccione ubicación...</option>
                                    <?php foreach($data['ubicaciones'] as $ubicacion): ?>
                                        <option value="<?php echo $ubicacion['id_ubicacion']; ?>">
                                            <?php echo $ubicacion['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cantidad *</label>
                                <input type="number" name="cantidad" class="form-control" min="1" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado Inicial</label>
                                <select name="estado" class="form-select">
                                    <option value="DISPONIBLE">Disponible</option>
                                    <option value="RESERVADO">Reservado</option>
                                    <option value="DAÑADO">Dañado</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha y Hora de Ingreso</label>
                                <input type="datetime-local" name="fecha_ingreso" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" readonly>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo URLROOT; ?>/inventario" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-success">Guardar Lote</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
