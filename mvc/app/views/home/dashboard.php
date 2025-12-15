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
    </div>

    <div class="row">
        <!-- Stock Bajo -->
        <div class="col-md-12">
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
                                        <th class="text-center">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($data['stock_bajo'], 0, 5) as $lote): ?>
                                        <tr>
                                            <td><span class="badge bg-secondary"><?php echo $lote['sku']; ?></span></td>
                                            <td><?php echo substr($lote['equipo_descripcion'], 0, 30); ?></td>
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
                        <?php endif; ?>
                        <a href="<?php echo URLROOT; ?>/movimientos/salida" class="btn btn-warning">
                            <i class="bi bi-arrow-up-circle"></i> Agregar nueva salida
                        </a>
                        <a href="<?php echo URLROOT; ?>/inventario/reporteDiario" class="btn btn-outline-danger">
                            <i class="bi bi-file-earmark-pdf"></i> Generar Reporte
                        </a>
                        <a href="<?php echo URLROOT; ?>/inventario" class="btn btn-info">
                            <i class="bi bi-boxes"></i> Ver Inventario
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
