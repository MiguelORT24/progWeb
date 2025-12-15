<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="bi bi-box-arrow-up"></i> Salidas de inventario</h2>
        <a class="btn btn-warning" href="<?php echo URLROOT; ?>/movimientos/salida">
            <i class="bi bi-arrow-up-circle"></i> Registrar nueva salida
        </a>
    </div>

    <?php if (!empty($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['tipo_mensaje'] ?? 'info'; ?>">
            <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>SKU</th>
                            <th>Equipo</th>
                            <th>Ubicación</th>
                            <th class="text-center">Cantidad</th>
                            <th>Usuario</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['movimientos'])): ?>
                            <tr><td colspan="7" class="text-center text-muted">Sin movimientos registrados</td></tr>
                        <?php else: ?>
                            <?php foreach ($data['movimientos'] as $mov): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($mov['fecha_hora'])); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo $mov['sku'] ?? 'N/A'; ?></span></td>
                                    <td><?php echo $mov['equipo_descripcion'] ?? '-'; ?></td>
                                    <td><?php echo $mov['ubicacion_nombre'] ?? '-'; ?></td>
                                    <td class="text-center"><span class="badge bg-danger"><?php echo $mov['cantidad']; ?></span></td>
                                    <td><?php echo $mov['usuario_nombre'] ?? 'Sistema'; ?></td>
                                    <td><?php echo $mov['motivo'] ?? '-'; ?></td>
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
