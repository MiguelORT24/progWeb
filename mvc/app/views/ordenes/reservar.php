<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <!-- Columna Izquierda: Detalles de Orden y Materiales Reservados -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Orden #<?php echo $data['orden']['id_orden']; ?> - <?php echo $data['orden']['cliente_nombre']; ?></h5>
                </div>
                <div class="card-body">
                    <p><strong>Fecha Programada:</strong> <?php echo date('d/m/Y', strtotime($data['orden']['fecha_programada'])); ?></p>
                    <p><strong>Estado:</strong> <span class="badge bg-warning text-dark"><?php echo $data['orden']['estado']; ?></span></p>
                    
                    <hr>
                    
                    <h6>Materiales Reservados</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Equipo</th>
                                    <th>Lote</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['materiales'])): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No hay materiales reservados aún</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($data['materiales'] as $mat): ?>
                                        <tr>
                                            <td><?php echo $mat['equipo_descripcion']; ?></td>
                                            <td>#<?php echo $mat['id_lote']; ?></td>
                                            <td><?php echo $mat['cantidad']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if(!empty($data['materiales'])): ?>
                        <div class="mt-3">
                            <form action="<?php echo URLROOT; ?>/ordenes/cambiarEstado/<?php echo $data['orden']['id_orden']; ?>" method="POST">
                                <input type="hidden" name="estado" value="EN_PROCESO">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-play-circle"></i> Iniciar Instalación (Pasar a EN PROCESO)
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Formulario de Reserva -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Reservar Material</h5>
                </div>
                <div class="card-body">
                    <?php flash('mensaje'); ?>
                    
                    <form action="<?php echo URLROOT; ?>/ordenes/reservar/<?php echo $data['orden']['id_orden']; ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Lote Disponible *</label>
                            <select name="id_lote" class="form-select" required>
                                <option value="">Seleccione lote...</option>
                                <?php foreach($data['lotes_disponibles'] as $lote): ?>
                                    <option value="<?php echo $lote['id_lote']; ?>">
                                        <?php echo $lote['sku'] . ' - ' . $lote['equipo_descripcion'] . ' (Stock: ' . $lote['cantidad'] . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cantidad a Reservar *</label>
                            <input type="number" name="cantidad" class="form-control" min="1" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Agregar Reserva
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
