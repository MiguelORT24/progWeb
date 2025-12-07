<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-boxes"></i> Gestión de Inventario</h1>
        <div>
            <a href="<?php echo URLROOT; ?>/reportes/diario" class="btn btn-outline-danger me-2" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> Generar Reporte 
            </a>
            <a href="<?php echo URLROOT; ?>/inventario/crear" 
               class="btn btn-success <?php echo !puedeCrear() ? 'disabled' : ''; ?>"
               <?php echo !puedeCrear() ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                <i class="bi bi-plus-circle"></i> Nuevo Lote
            </a>
        </div>
    </div>

    <?php flash('mensaje'); ?>

    <!-- Filtros de Búsqueda -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo URLROOT; ?>/inventario" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">SKU / Descripción</label>
                    <input type="text" name="sku" class="form-control" value="<?php echo $data['filtros']['sku'] ?? ''; ?>" placeholder="Buscar...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="CAMARA" <?php echo ($data['filtros']['tipo'] == 'CAMARA') ? 'selected' : ''; ?>>Cámaras</option>
                        <option value="SENSOR" <?php echo ($data['filtros']['tipo'] == 'SENSOR') ? 'selected' : ''; ?>>Sensores</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Marca</label>
                    <select name="marca" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach($data['marcas'] as $marca): ?>
                            <option value="<?php echo $marca['id_marca']; ?>" <?php echo ($data['filtros']['marca'] == $marca['id_marca']) ? 'selected' : ''; ?>>
                                <?php echo $marca['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Productos Agrupados -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>SKU</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Marca</th>
                            <th class="text-center">Disponible</th>
                            <th class="text-center">Reservado</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Lotes</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['productos'])): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">No se encontraron productos</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['productos'] as $producto): ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?php echo $producto['sku']; ?></span></td>
                                    <td>
                                        <?php if($producto['tipo'] == 'CAMARA'): ?>
                                            <i class="bi bi-camera-video text-primary"></i> Cámara
                                        <?php else: ?>
                                            <i class="bi bi-wifi text-success"></i> Sensor
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $producto['descripcion']; ?></td>
                                    <td><?php echo $producto['marca_nombre'] ?? '-'; ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-success"><?php echo $producto['cantidad_disponible']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark"><?php echo $producto['cantidad_reservada']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-primary"><?php echo $producto['cantidad_total']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info"><?php echo $producto['total_lotes']; ?></span>
                                    </td>
                                    <td>
                                        <a href="<?php echo URLROOT; ?>/inventario/verLotes/<?php echo $producto['id_equipo']; ?>" class="btn btn-sm btn-primary" title="Ver Lotes">
                                            <i class="bi bi-eye"></i> Ver Lotes
                                        </a>
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
