<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
            <p class="text-muted">Bienvenido, <strong><?php echo $data['usuario']; ?></strong> (<?php echo $data['rol']; ?>)</p>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-box"></i> Total Productos</h6>
                    <h2><?php echo $data['total_productos']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-boxes"></i> Total Unidades</h6>
                    <h2><?php echo $data['total_unidades']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-exclamation-triangle"></i> Stock Bajo</h6>
                    <h2><?php echo count($data['stock_bajo']); ?></h2>
                </div>
            </div>
        </div>
        <?php if (puedeConfirmar()): ?>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-clock"></i> Salidas Pendientes</h6>
                    <h2><?php echo count($data['compras_pendientes']); ?></h2>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <!-- Stock Bajo -->
        <div class="col-md-<?php echo puedeConfirmar() ? '6' : '12'; ?>">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Productos con Stock Bajo</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($data['stock_bajo'])): ?>
                        <p class="text-muted">No hay productos con stock bajo</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Producto</th>
                                        <th>Ubicación</th>
                                        <th class="text-center">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($data['stock_bajo'], 0, 5) as $lote): ?>
                                        <tr>
                                            <td><span class="badge bg-secondary"><?php echo $lote['sku']; ?></span></td>
                                            <td><?php echo substr($lote['equipo_descripcion'], 0, 30); ?></td>
                                            <td><?php echo $lote['ubicacion_nombre']; ?></td>
                                            <td class="text-center"><span class="badge bg-danger"><?php echo $lote['cantidad']; ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?php echo URLROOT; ?>/inventario" class="btn btn-sm btn-warning">Ver Todo el Inventario</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Ventas Pendientes (solo para Admin/Almacén) -->
        <?php if (puedeConfirmar()): ?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clock"></i> Salidas Pendientes de Confirmación</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($data['compras_pendientes'])): ?>
                        <p class="text-muted">No hay salidas pendientes</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($data['compras_pendientes'], 0, 5) as $venta): ?>
                                        <tr>
                                            <td>#<?php echo $venta['id_compra']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($venta['fecha'])); ?></td>
                                            <td><?php echo $venta['proveedor_nombre']; ?></td>
                                            <td class="text-end">$<?php echo number_format($venta['total'], 2); ?></td>
                                            <td class="text-center">
                                                <a href="<?php echo URLROOT; ?>/ventas/confirmar/<?php echo $venta['id_compra']; ?>" class="btn btn-sm btn-success">Confirmar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?php echo URLROOT; ?>/ventas" class="btn btn-sm btn-info">Ver Todas las Salidas</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <?php if (puedeCrear()): ?>
                            <a href="<?php echo URLROOT; ?>/inventario/crear" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Nuevo Lote
                            </a>
                            <a href="<?php echo URLROOT; ?>/ventas/crear" class="btn btn-primary">
                                <i class="bi bi-cart-plus"></i> Nueva Salida
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo URLROOT; ?>/inventario" class="btn btn-info">
                            <i class="bi bi-boxes"></i> Ver Inventario
                        </a>
                        <a href="<?php echo URLROOT; ?>/reportes/diario" class="btn btn-outline-danger" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Generar Reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
