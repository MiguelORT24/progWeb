<?php require_once APPROOT . '/views/layouts/header.inc.php'; ?>

<h2><i class="fa fa-exchange-alt"></i> Movimientos de Inventario</h2>

<?php if(!estaLogueado()){ ?>
    <div class="alert alert-info">Usted no está autorizado!!!</div>
<?php } else { ?>

<div class="row mb-3">
    <div class="col-8"><h4>Historial de Movimientos</h4></div>
    <div class="col-4 text-end">
        <a href="<?= URLROOT ?>/reportes/movimientosHoy" class="btn btn-primary btn-sm">
            <i class="fa fa-file-pdf"></i> Generar reporte
        </a>
        <a href="<?= URLROOT ?>/movimientos/entrada" class="btn btn-success btn-sm">
            <i class="fa fa-arrow-down"></i> Entrada
        </a>
        <a href="<?= URLROOT ?>/movimientos/salida" class="btn btn-warning btn-sm">
            <i class="fa fa-arrow-up"></i> Salida
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Total</th>
                <th>Usuario</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $mov): ?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($mov['created_at'])) ?></td>
                <td><?= $mov['producto_nombre'] ?></td>
                <td>
                    <?php if($mov['movimiento_tipo'] == 'entrada'): ?>
                        <span class="badge bg-success">Entrada</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Salida</span>
                    <?php endif; ?>
                </td>
                <td><?= $mov['movimiento_cantidad'] ?></td>
                <td>$<?= number_format($mov['movimiento_precio_unitario'], 2) ?></td>
                <td>$<?= number_format($mov['movimiento_total'], 2) ?></td>
                <td><?= $mov['usuario_nombre'] ?? 'Sistema' ?></td>
                <td><?= $mov['movimiento_motivo'] ?? '-' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php } ?>

<?php require_once APPROOT . '/views/layouts/footer.inc.php'; ?>
