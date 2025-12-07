<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-cart"></i> Salidas </h1>
        <!-- <a href="<?php echo URLROOT; ?>/compras/crear" class="btn btn-primary">
            <i class="bi bi-cart-plus"></i> Nueva Compra
        </a> -->
    </div>

    <?php flash('mensaje'); ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['compras'])): ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay compras registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['compras'] as $compra): ?>
                                <tr>
                                    <td>#<?php echo $compra['id_compra']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($compra['fecha'])); ?></td>
                                    <td><?php echo $compra['proveedor_nombre']; ?></td>
                                    <td>$<?php echo number_format($compra['total'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo $compra['estado'] == 'CONFIRMADA' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                            <?php echo $compra['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo URLROOT; ?>/compras/ver/<?php echo $compra['id_compra']; ?>" class="btn btn-sm btn-info" title="Ver Detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if($compra['estado'] == 'PENDIENTE'): ?>
                                            <a href="<?php echo URLROOT; ?>/compras/confirmar/<?php echo $compra['id_compra']; ?>" class="btn btn-sm btn-success" title="Confirmar">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                            <a href="<?php echo URLROOT; ?>/compras/eliminar/<?php echo $compra['id_compra']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar compra?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
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
