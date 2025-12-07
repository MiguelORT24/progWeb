<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="bi bi-pencil"></i> Editar Lote #<?php echo $data['lote']['id_lote']; ?></h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/inventario/editar/<?php echo $data['lote']['id_lote']; ?>" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Equipo</label>
                            <select name="id_equipo" class="form-select" required>
                                <?php foreach($data['equipos'] as $equipo): ?>
                                    <option value="<?php echo $equipo['id_equipo']; ?>" <?php echo ($data['lote']['id_equipo'] == $equipo['id_equipo']) ? 'selected' : ''; ?>>
                                        <?php echo $equipo['sku'] . ' - ' . $equipo['descripcion']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ubicación</label>
                                <select name="id_ubicacion" class="form-select" required>
                                    <?php foreach($data['ubicaciones'] as $ubicacion): ?>
                                        <option value="<?php echo $ubicacion['id_ubicacion']; ?>" <?php echo ($data['lote']['id_ubicacion'] == $ubicacion['id_ubicacion']) ? 'selected' : ''; ?>>
                                            <?php echo $ubicacion['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cantidad</label>
                                <input type="number" name="cantidad" class="form-control" min="0" value="<?php echo $data['lote']['cantidad']; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select">
                                    <option value="DISPONIBLE" <?php echo ($data['lote']['estado'] == 'DISPONIBLE') ? 'selected' : ''; ?>>Disponible</option>
                                    <option value="RESERVADO" <?php echo ($data['lote']['estado'] == 'RESERVADO') ? 'selected' : ''; ?>>Reservado</option>
                                    <option value="DAÑADO" <?php echo ($data['lote']['estado'] == 'DAÑADO') ? 'selected' : ''; ?>>Dañado</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Ingreso</label>
                                <input type="date" name="fecha_ingreso" class="form-control" value="<?php echo date('Y-m-d', strtotime($data['lote']['fecha_ingreso'])); ?>" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo URLROOT; ?>/inventario" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-warning">Actualizar Lote</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
