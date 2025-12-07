<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-receipt"></i> Detalle de Salida #<?php echo $data['venta']['id_compra']; ?></h2>
        <a href="<?php echo URLROOT; ?>/ventas" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="row">
        <!-- Información de la Salida -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <p><strong>Estado:</strong> 
                        <?php
                            $clase = 'secondary';
                            switch($data['venta']['estado']) {
                                case 'PENDIENTE': $clase = 'warning'; break;
                                case 'CONFIRMADA': $clase = 'success'; break;
                                case 'CANCELADA': $clase = 'danger'; break;
                            }
                        ?>
                        <span class="badge bg-<?php echo $clase; ?>"><?php echo $data['venta']['estado']; ?></span>
                    </p>
                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($data['venta']['fecha'])); ?></p>
                    <p><strong>Cliente:</strong> <?php echo $data['venta']['proveedor_nombre']; ?></p>
                    <p><strong>Contacto:</strong> <?php echo $data['venta']['proveedor_contacto'] ?? '-'; ?></p>
                    <p><strong>Teléfono:</strong> <?php echo $data['venta']['proveedor_telefono'] ?? '-'; ?></p>
                </div>
            </div>

            <!-- Acciones -->
            <?php if($data['venta']['estado'] == 'PENDIENTE'): ?>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    <a href="<?php echo URLROOT; ?>/ventas/confirmar/<?php echo $data['venta']['id_compra']; ?>" class="btn btn-success w-100 mb-2">
                        <i class="bi bi-check-circle"></i> Confirmar Salida
                    </a>
                    <form action="<?php echo URLROOT; ?>/ventas/eliminar/<?php echo $data['venta']['id_compra']; ?>" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta salida?')">
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
                    <h5 class="mb-0">Productos de la Salida</h5>
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['detalle'])): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No hay productos en esta salida</td>
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
                                        </tr>
                                    <?php endforeach; ?>

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
