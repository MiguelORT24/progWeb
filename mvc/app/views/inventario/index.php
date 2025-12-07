<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-boxes"></i> Gestión de Inventario</h1>
        <div>
            <a href="<?php echo URLROOT; ?>/reportes/diario" class="btn btn-outline-danger me-2" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> Generar Reporte 
            </a>
            <a href="<?php echo URLROOT; ?>/inventario/crear" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Nuevo Lote
            </a>
        </div>
    </div>

    <?php flash('mensaje'); ?>

    <!-- Filtros de Búsqueda (UI-INV-01) -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo URLROOT; ?>/inventario" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">SKU / Descripción</label>
                    <input type="text" name="sku" class="form-control" value="<?php echo $data['filtros']['sku'] ?? ''; ?>" placeholder="Buscar...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ubicación</label>
                    <select name="ubicacion" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach($data['ubicaciones'] as $ubicacion): ?>
                            <option value="<?php echo $ubicacion['id_ubicacion']; ?>" <?php echo ($data['filtros']['id_ubicacion'] == $ubicacion['id_ubicacion']) ? 'selected' : ''; ?>>
                                <?php echo $ubicacion['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="DISPONIBLE" <?php echo ($data['filtros']['estado'] == 'DISPONIBLE') ? 'selected' : ''; ?>>Disponible</option>
                        <option value="RESERVADO" <?php echo ($data['filtros']['estado'] == 'RESERVADO') ? 'selected' : ''; ?>>Reservado</option>
                        <option value="INSTALADO" <?php echo ($data['filtros']['estado'] == 'INSTALADO') ? 'selected' : ''; ?>>Instalado</option>
                        <option value="DAÑADO" <?php echo ($data['filtros']['estado'] == 'DAÑADO') ? 'selected' : ''; ?>>Dañado</option>
                    </select>
                </div>
                <div class="col-md-2">
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

    <!-- Tabla de Resultados -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Lote ID</th>
                            <th>SKU</th>
                            <th>Equipo</th>
                            <th>Ubicación</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                            <th>Fecha Ingreso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['lotes'])): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">No se encontraron lotes</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['lotes'] as $lote): ?>
                                <tr>
                                    <td>#<?php echo $lote['id_lote']; ?></td>
                                    <td><span class="badge bg-secondary"><?php echo $lote['sku']; ?></span></td>
                                    <td><?php echo $lote['equipo_descripcion']; ?></td>
                                    <td><i class="bi bi-geo-alt"></i> <?php echo $lote['ubicacion_nombre']; ?></td>
                                    <td>
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
                                                case 'INSTALADO': $clase = 'bg-primary'; break;
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
                                            <a href="<?php echo URLROOT; ?>/inventario/editar/<?php echo $lote['id_lote']; ?>" class="btn btn-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form action="<?php echo URLROOT; ?>/inventario/cambiarEstado/<?php echo $lote['id_lote']; ?>" method="POST">
                                                        <input type="hidden" name="estado" value="DISPONIBLE">
                                                        <button class="dropdown-item" type="submit">Marcar Disponible</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="<?php echo URLROOT; ?>/inventario/cambiarEstado/<?php echo $lote['id_lote']; ?>" method="POST">
                                                        <input type="hidden" name="estado" value="DAÑADO">
                                                        <button class="dropdown-item text-danger" type="submit">Marcar Dañado</button>
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
