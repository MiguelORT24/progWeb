<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-box-seam"></i> Lotes de: <?php echo $data['equipo']['sku']; ?></h2>
        <a href="<?php echo URLROOT; ?>/inventario" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Inventario
        </a>
    </div>

    <!-- Información del Producto -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Información del Producto</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>SKU:</strong> <span class="badge bg-secondary"><?php echo $data['equipo']['sku']; ?></span>
                </div>
                <div class="col-md-3">
                    <strong>Tipo:</strong> 
                    <?php if($data['equipo']['tipo'] == 'CAMARA'): ?>
                        <i class="bi bi-camera-video text-primary"></i> Cámara
                    <?php else: ?>
                        <i class="bi bi-wifi text-success"></i> Sensor
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <strong>Marca:</strong> <?php echo $data['equipo']['marca_nombre'] ?? '-'; ?>
                </div>
                <div class="col-md-3">
                    <strong>Categoría:</strong> <?php echo $data['equipo']['categoria_nombre'] ?? '-'; ?>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <strong>Descripción:</strong> <?php echo $data['equipo']['descripcion']; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Lotes -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Lotes Individuales</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Lote ID</th>
                            <th>Ubicación</th>
                            <th class="text-center">Cantidad</th>
                            <th>Estado</th>
                            <th>Fecha Ingreso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['lotes'])): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No hay lotes para este producto</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['lotes'] as $lote): ?>
                                <tr>
                                    <td>#<?php echo $lote['id_lote']; ?></td>
                                    <td><i class="bi bi-geo-alt"></i> <?php echo $lote['ubicacion_nombre']; ?></td>
                                    <td class="text-center">
                                        <span class="fw-bold <?php echo $lote['cantidad'] < 5 ? 'text-danger' : 'text-success'; ?>">
                                            <?php echo $lote['cantidad']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                            $clase = 'bg-secondary';
                                            switch($lote['estado']) {
                                                case 'DISPONIBLE': $clase = 'bg-success'; break;
                                                case 'RESERVADO': $clase = 'bg-warning text-dark'; break;
                                                case 'DAÑADO': $clase = 'bg-danger'; break;
                                            }
                                        ?>
                                        <span class="badge <?php echo $clase; ?>"><?php echo $lote['estado']; ?></span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($lote['fecha_ingreso'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo URLROOT; ?>/inventario/historial/<?php echo $lote['id_lote']; ?>" class="btn btn-info" title="Historial">
                                                <i class="bi bi-clock-history"></i>
                                            </a>
                                            <a href="<?php echo URLROOT; ?>/inventario/editar/<?php echo $lote['id_lote']; ?>" 
                                               class="btn btn-warning <?php echo !puedeEditar() ? 'disabled' : ''; ?>" 
                                               title="Editar"
                                               <?php echo !puedeEditar() ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-secondary dropdown-toggle dropdown-toggle-split <?php echo !puedeEditar() ? 'disabled' : ''; ?>" 
                                                    data-bs-toggle="dropdown"
                                                    <?php echo !puedeEditar() ? 'disabled' : ''; ?>>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form action="<?php echo URLROOT; ?>/inventario/cambiarEstado/<?php echo $lote['id_lote']; ?>" method="POST">
                                                        <input type="hidden" name="estado" value="DISPONIBLE">
                                                        <button class="dropdown-item" type="submit" <?php echo !puedeEditar() ? 'disabled' : ''; ?>>Marcar Disponible</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="<?php echo URLROOT; ?>/inventario/cambiarEstado/<?php echo $lote['id_lote']; ?>" method="POST">
                                                        <input type="hidden" name="estado" value="RESERVADO">
                                                        <button class="dropdown-item text-warning" type="submit" <?php echo !puedeEditar() ? 'disabled' : ''; ?>>Marcar Reservado</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="<?php echo URLROOT; ?>/inventario/cambiarEstado/<?php echo $lote['id_lote']; ?>" method="POST">
                                                        <input type="hidden" name="estado" value="DAÑADO">
                                                        <button class="dropdown-item text-danger" type="submit" <?php echo !puedeEditar() ? 'disabled' : ''; ?>>Marcar Dañado</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
