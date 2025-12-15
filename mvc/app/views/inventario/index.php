<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-boxes"></i> Gestión de Inventario</h1>
        <div>
            <a href="<?php echo URLROOT; ?>/inventario/reporteDiario" class="btn btn-outline-danger me-2">
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
                <div class="col-md-3">
                    <label class="form-label">SKU / Descripción</label>
                    <input type="text" name="sku" class="form-control" value="<?php echo $data['filtros']['sku'] ?? ''; ?>" placeholder="Buscar...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach($data['tipos'] as $tipo): ?>
                            <option value="<?php echo $tipo; ?>" <?php echo ($data['filtros']['tipo'] == $tipo) ? 'selected' : ''; ?>>
                                <?php echo ucfirst(strtolower($tipo)); ?>
                            </option>
                        <?php endforeach; ?>
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
                        <?php if(!empty($data['filtros']['marca'])): ?>
                            <?php $existeMarca = array_filter($data['marcas'], fn($m) => (string)$m['id_marca'] === (string)$data['filtros']['marca']); ?>
                            <?php if(empty($existeMarca)): ?>
                                <option value="<?php echo $data['filtros']['marca']; ?>" selected>Marca #<?php echo $data['filtros']['marca']; ?></option>
                            <?php endif; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <!-- <div class="col-md-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria" class="form-select">
                        <option value="">Todas</option>
                        < ?php foreach($data['categorias'] as $categoria): ?>
                            <option value="< ?php echo $categoria['id_categoria']; ?>" < ?php echo ($data['filtros']['categoria'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                                < ?php echo $categoria['nombre']; ?>
                            </option>
                        < ?php endforeach; ?>
                        < ?php if(!empty($data['filtros']['categoria'])): ?>
                            < ?php $existeCat = array_filter($data['categorias'], fn($c) => (string)$c['id_categoria'] === (string)$data['filtros']['categoria']); ?>
                            < ?php if(empty($existeCat)): ?>
                                <option value="< ?php echo $data['filtros']['categoria']; ?>" selected>Categoría #< ?php echo $data['filtros']['categoria']; ?></option>
                            < ?php endif; ?>
                        < ?php endif; ?>
                    </select>
                </div> -->
                <div class="col-md-12 d-flex justify-content-end">
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
                                        <?php 
                                            $tipo = strtoupper($producto['tipo'] ?? '');
                                            if ($tipo === 'CAMARA') {
                                                echo '<i class="bi bi-camera-video text-primary"></i> Cámara';
                                            } elseif ($tipo === 'SENSOR') {
                                                echo '<i class="bi bi-wifi text-success"></i> Sensor';
                                            } elseif ($tipo === 'COMPONENTE') {
                                                echo '<i class="bi bi-cpu text-secondary"></i> Componente';
                                            } else {
                                                echo '<i class="bi bi-question-circle text-muted"></i> ' . htmlspecialchars($producto['tipo'] ?? '-');
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo $producto['descripcion']; ?></td>
                                    <td><?php echo $producto['marca_nombre'] ?? '-'; ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-success"><?php echo $producto['cantidad_disponible'] ?? 0; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark"><?php echo $producto['cantidad_reservada'] ?? 0; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-primary"><?php echo $producto['cantidad_total'] ?? 0; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info"><?php echo $producto['total_lotes'] ?? 0; ?></span>
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
