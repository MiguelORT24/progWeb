<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-box-arrow-right"></i> Gestión de Salidas</h1>
        <a href="<?php echo URLROOT; ?>/ventas/crear" 
           class="btn btn-primary <?php echo !puedeCrear() ? 'disabled' : ''; ?>"
           <?php echo !puedeCrear() ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
            <i class="bi bi-plus-circle"></i> Nueva Salida
        </a>
    </div>

    <?php flash('mensaje'); ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['ventas'])): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No hay salidas registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['ventas'] as $venta): ?>
                                <tr>
                                    <td>#<?php echo $venta['id_compra']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?></td>
                                    <td class="text-center">
                                        <?php
                                            $clase = 'secondary';
                                            switch($venta['estado']) {
                                                case 'PENDIENTE': $clase = 'warning'; break;
                                                case 'CONFIRMADA': $clase = 'success'; break;
                                                case 'CANCELADA': $clase = 'danger'; break;
                                            }
                                        ?>
                                        <span class="badge bg-<?php echo $clase; ?>"><?php echo $venta['estado']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo URLROOT; ?>/ventas/ver/<?php echo $venta['id_compra']; ?>" class="btn btn-info" title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if($venta['estado'] == 'PENDIENTE'): ?>
                                                <a href="<?php echo URLROOT; ?>/ventas/confirmar/<?php echo $venta['id_compra']; ?>" 
                                                   class="btn btn-success <?php echo !puedeConfirmar() ? 'disabled' : ''; ?>" 
                                                   title="Confirmar"
                                                   <?php echo !puedeConfirmar() ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                                <form action="<?php echo URLROOT; ?>/ventas/eliminar/<?php echo $venta['id_compra']; ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar esta salida?')">
                                                    <button type="submit" 
                                                            class="btn btn-danger <?php echo !puedeEliminar() ? 'disabled' : ''; ?>" 
                                                            title="Eliminar"
                                                            <?php echo !puedeEliminar() ? 'disabled' : ''; ?>>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
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
