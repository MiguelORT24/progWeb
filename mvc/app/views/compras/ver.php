<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-receipt"></i> Detalle de Compra #<?php echo $data['compra']['id_compra']; ?></h2>
        <a href="<?php echo URLROOT; ?>/compras" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="row">
        <!-- Información de la Compra -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <p><strong>Estado:</strong> 
                        <?php
                            $clase = 'secondary';
                            switch($data['compra']['estado']) {
                                case 'PENDIENTE': $clase = 'warning'; break;
                                case 'CONFIRMADA': $clase = 'success'; break;
                                case 'CANCELADA': $clase = 'danger'; break;
                            }
                        ?>
                        <span class="badge bg-<?php echo $clase; ?>"><?php echo $data['compra']['estado']; ?></span>
                    </p>
                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($data['compra']['fecha'])); ?></p>
                    <p><strong>Proveedor:</strong> <?php echo $data['compra']['proveedor_nombre']; ?></p>
                    <p><strong>Contacto:</strong> <?php echo $data['compra']['proveedor_contacto'] ?? '-'; ?></p>
                    <p><strong>Teléfono:</strong> <?php echo $data['compra']['proveedor_telefono'] ?? '-'; ?></p>
                    <hr>
                    <h5 class="text-primary">Total: $<?php echo number_format($data['compra']['total'], 2); ?></h5>
                </div>
            </div>

            <!-- Acciones -->
            <?php if($data['compra']['estado'] == 'PENDIENTE'): ?>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    <a href="<?php echo URLROOT; ?>/compras/confirmar/<?php echo $data['compra']['id_compra']; ?>" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-check-circle"></i> Confirmar Compra
                    </a>
                    <form action="<?php echo URLROOT; ?>/compras/eliminar/<?php echo $data['compra']['id_compra']; ?>" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta compra?')">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Detalle de Productos -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Productos de la Compra</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>SKU</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Costo Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['detalle'])): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No hay productos en esta compra</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($data['detalle'] as $item): ?>
                                        <tr>
                                            <td><span class="badge bg-secondary"><?php echo $item['sku']; ?></span></td>
                                            <td>
                                                <?php if($item['tipo'] == 'CAMARA'): ?>
                                                    <i class="bi bi-camera-video text-primary"></i> Cámara
                                                <?php else: ?>
                                                    <i class="bi bi-wifi text-success"></i> Sensor
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $item['equipo_descripcion']; ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-info"><?php echo $item['cantidad']; ?></span>
                                            </td>
                                            <td class="text-end">$<?php echo number_format($item['costo_unitario'], 2); ?></td>
                                            <td class="text-end fw-bold">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-light">
                                        <td colspan="5" class="text-end fw-bold">TOTAL:</td>
                                        <td class="text-end fw-bold text-primary fs-5">
                                            $<?php echo number_format($data['compra']['total'], 2); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
