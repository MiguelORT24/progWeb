<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-clipboard-check"></i> Órdenes de Instalación</h1>
        <a href="<?php echo URLROOT; ?>/ordenes/crear" class="btn btn-warning">
            <i class="bi bi-plus-circle"></i> Nueva Orden
        </a>
    </div>

    <?php flash('mensaje'); ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Fecha Programada</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['ordenes'])): ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay órdenes registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['ordenes'] as $orden): ?>
                                <tr>
                                    <td>#<?php echo $orden['id_orden']; ?></td>
                                    <td><?php echo $orden['cliente_nombre']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($orden['fecha_programada'])); ?></td>
                                    <td>
                                        <?php
                                            $clase = 'bg-secondary';
                                            switch($orden['estado']) {
                                                case 'PENDIENTE': $clase = 'bg-warning text-dark'; break;
                                                case 'EN_PROCESO': $clase = 'bg-info'; break;
                                                case 'COMPLETADA': $clase = 'bg-success'; break;
                                                case 'CANCELADA': $clase = 'bg-danger'; break;
                                            }
                                        ?>
                                        <span class="badge <?php echo $clase; ?>"><?php echo $orden['estado']; ?></span>
                                    </td>
                                    <td><?php echo $orden['usuario_nombre']; ?></td>
                                    <td>
                                        <a href="<?php echo URLROOT; ?>/ordenes/ver/<?php echo $orden['id_orden']; ?>" class="btn btn-sm btn-info" title="Ver Detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if($orden['estado'] == 'PENDIENTE'): ?>
                                            <a href="<?php echo URLROOT; ?>/ordenes/reservar/<?php echo $orden['id_orden']; ?>" class="btn btn-sm btn-primary" title="Reservar Materiales">
                                                <i class="bi bi-box-seam"></i>
                                            </a>
                                            <a href="<?php echo URLROOT; ?>/ordenes/eliminar/<?php echo $orden['id_orden']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar orden?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if($orden['estado'] == 'EN_PROCESO'): ?>
                                            <a href="<?php echo URLROOT; ?>/ordenes/confirmar/<?php echo $orden['id_orden']; ?>" class="btn btn-sm btn-success" title="Confirmar Instalación" onclick="return confirm('¿Confirmar instalación? Esto descontará el stock reservado.');">
                                                <i class="bi bi-check-lg"></i>
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
